<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = LeaveRequest::where('user_id', $user->id);

        if ($request->filled('filter_start_date')) {
            $query->where('start_date', '>=', $request->filter_start_date);
        }
        if ($request->filled('filter_end_date')) {
            $query->where('end_date', '<=', $request->filter_end_date);
        }

        $requests = $query->orderBy('start_date', 'desc')->get();

        // Calculate remaining cuti for the current year
        $currentYear = date('Y');
        $approvedLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('type', 'cuti')
            ->where('status', 'approved')
            ->where(function ($q) use ($currentYear) {
                $q->whereYear('start_date', $currentYear)
                  ->orWhereYear('end_date', $currentYear);
            })
            ->get();

        $usedDays = 0;
        foreach ($approvedLeaves as $leave) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            
            $yrStart = Carbon::create($currentYear, 1, 1);
            $yrEnd = Carbon::create($currentYear, 12, 31);
            
            $overlapStart = $start->greaterThan($yrStart) ? $start : $yrStart;
            $overlapEnd = $end->lessThan($yrEnd) ? $end : $yrEnd;
            
            if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                $temp = $overlapStart->copy();
                while ($temp->lessThanOrEqualTo($overlapEnd)) {
                    if (!$temp->isSaturday() && !$temp->isSunday()) {
                        $usedDays++;
                    }
                    $temp->addDay();
                }
            }
        }
        $remainingCuti = max(0, 7 - $usedDays);

        return view('leave-requests.index', compact('requests', 'remainingCuti'));
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:izin,cuti',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ];

        if ($request->type === 'izin') {
            $rules['proof'] = 'required|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120';
        } else {
            $rules['proof'] = 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120';
        }

        $request->validate($rules);

        if (Auth::user()->hasRole('Intern') && $request->type === 'cuti') {
            return redirect()->back()->withErrors(['type' => 'Anak magang (Intern) hanya diperbolehkan mengajukan izin, tidak boleh mengajukan cuti.'])->withInput();
        }

        if ($request->type === 'cuti') {
            // Validate that the request contains at least 1 working day (Monday - Friday)
            $newStart = Carbon::parse($request->start_date);
            $newEnd = Carbon::parse($request->end_date);
            $workDays = 0;
            $temp = $newStart->copy();
            while ($temp->lessThanOrEqualTo($newEnd)) {
                if (!$temp->isSaturday() && !$temp->isSunday()) {
                    $workDays++;
                }
                $temp->addDay();
            }
            if ($workDays === 0) {
                return redirect()->back()->withErrors(['end_date' => 'Pengajuan cuti harus mencakup minimal 1 hari kerja (Senin - Jumat).'])->withInput();
            }

            $limitError = $this->checkCutiLimit(Auth::user(), $request->start_date, $request->end_date);
            if ($limitError) {
                return redirect()->back()->withErrors(['end_date' => $limitError])->withInput();
            }
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');
            $filename = 'proof_' . Auth::id() . '_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('assets/proofs');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }
            
            $file->move($destinationPath, $filename);
            $proofPath = '/assets/proofs/' . $filename;
        }

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'proof_path' => $proofPath,
            'status' => 'pending',
        ]);

        return redirect()->route('leave-requests.index')->with('success', 'Pengajuan izin/cuti berhasil dikirim.');
    }

    public function approvals(Request $request)
    {
        $user = Auth::user();
        
        // CEO can approve cuti & izin
        // GM can only approve izin
        $queryPending = LeaveRequest::with('user')->where('status', 'pending');
        $queryHistory = LeaveRequest::with(['user', 'approvedBy'])->where('status', '!=', 'pending');

        if ($user->hasRole('CEO')) {
            // Can see all
        } elseif ($user->hasRole('GM')) {
            $queryPending->where('type', 'izin');
            $queryHistory->where('type', 'izin');
        } else {
            abort(403, 'Unauthorized access.');
        }

        if ($request->filled('filter_start_date')) {
            $queryHistory->where('start_date', '>=', $request->filter_start_date);
        }
        if ($request->filled('filter_end_date')) {
            $queryHistory->where('end_date', '<=', $request->filter_end_date);
        }

        $pendingRequests = $queryPending->orderBy('created_at', 'asc')->get();

        if ($request->filled('filter_start_date') || $request->filled('filter_end_date')) {
            $historyRequests = $queryHistory->orderBy('updated_at', 'desc')->get();
        } else {
            $historyRequests = $queryHistory->orderBy('updated_at', 'desc')->take(20)->get();
        }

        return view('leave-requests.approvals', compact('pendingRequests', 'historyRequests'));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Authorization checks
        if ($leaveRequest->type === 'cuti' && !$user->hasRole('CEO')) {
            return redirect()->back()->with('error', 'Persetujuan cuti hanya dapat disetujui oleh CEO.');
        }

        if (!$user->hasRole(['CEO', 'GM'])) {
            abort(403);
        }

        if ($leaveRequest->type === 'cuti') {
            $limitError = $this->checkCutiLimit($leaveRequest->user, $leaveRequest->start_date, $leaveRequest->end_date, $leaveRequest->id);
            if ($limitError) {
                return redirect()->back()->with('error', 'Tidak dapat menyetujui. ' . $limitError);
            }
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by_id' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin/cuti berhasil disetujui.');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Authorization checks
        if ($leaveRequest->type === 'cuti' && !$user->hasRole('CEO')) {
            return redirect()->back()->with('error', 'Penolakan cuti hanya dapat dilakukan oleh CEO.');
        }

        if (!$user->hasRole(['CEO', 'GM'])) {
            abort(403);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by_id' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin/cuti telah ditolak.');
    }

    /**
     * Check if a leave request (or approving it) would exceed the 7-day annual cuti limit.
     * Returns error message if exceeded, null otherwise.
     */
    private function checkCutiLimit(User $user, $startDate, $endDate, $excludeRequestId = null): ?string
    {
        $newStart = Carbon::parse($startDate);
        $newEnd = Carbon::parse($endDate);
        
        $years = array_unique([$newStart->year, $newEnd->year]);
        
        foreach ($years as $yr) {
            $query = LeaveRequest::where('user_id', $user->id)
                ->where('type', 'cuti')
                ->where('status', 'approved')
                ->where(function ($q) use ($yr) {
                    $q->whereYear('start_date', $yr)
                      ->orWhereYear('end_date', $yr);
                });
                
            if ($excludeRequestId) {
                $query->where('id', '!=', $excludeRequestId);
            }
            
            $approvedLeaves = $query->get();
            
            $usedDays = 0;
            foreach ($approvedLeaves as $leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);
                
                $yrStart = Carbon::create($yr, 1, 1);
                $yrEnd = Carbon::create($yr, 12, 31);
                
                $overlapStart = $start->greaterThan($yrStart) ? $start : $yrStart;
                $overlapEnd = $end->lessThan($yrEnd) ? $end : $yrEnd;
                
                if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                    $temp = $overlapStart->copy();
                    while ($temp->lessThanOrEqualTo($overlapEnd)) {
                        if (!$temp->isSaturday() && !$temp->isSunday()) {
                            $usedDays++;
                        }
                        $temp->addDay();
                    }
                }
            }
            
            $yrStart = Carbon::create($yr, 1, 1);
            $yrEnd = Carbon::create($yr, 12, 31);
            
            $newOverlapStart = $newStart->greaterThan($yrStart) ? $newStart : $yrStart;
            $newOverlapEnd = $newEnd->lessThan($yrEnd) ? $newEnd : $yrEnd;
            
            $newDaysInYear = 0;
            if ($newOverlapStart->lessThanOrEqualTo($newOverlapEnd)) {
                $temp = $newOverlapStart->copy();
                while ($temp->lessThanOrEqualTo($newOverlapEnd)) {
                    if (!$temp->isSaturday() && !$temp->isSunday()) {
                        $newDaysInYear++;
                    }
                    $temp->addDay();
                }
            }
            
            if ($usedDays + $newDaysInYear > 7) {
                $remaining = 7 - $usedDays;
                return "Batas maksimal cuti dalam setahun adalah 7 hari. Pengguna ini telah menggunakan {$usedDays} hari di tahun {$yr}. Sisa kuota cuti untuk tahun {$yr} adalah {$remaining} hari.";
            }
        }
        
        return null;
    }

    public function downloadPdf(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        if ($leaveRequest->user_id !== $user->id && !$user->hasAnyRole(['CEO', 'GM', 'Admin', 'Superadmin'])) {
            abort(403);
        }

        if ($leaveRequest->type !== 'cuti' || $leaveRequest->status !== 'approved') {
            abort(400, 'Hanya cuti yang disetujui yang dapat diunduh.');
        }

        // Set locale to ID for Carbon translations in PDF
        \Carbon\Carbon::setLocale('id');

        // Fetch CEO and GM roles safely without throwing exceptions if roles are not created
        $ceo = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'CEO');
        })->first();
        $gm = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'GM');
        })->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('leave-requests.pdf', compact('leaveRequest', 'ceo', 'gm'));
        return $pdf->download('Surat_Cuti_' . Str::slug($leaveRequest->user->name) . '_' . $leaveRequest->start_date->format('Ymd') . '.pdf');
    }
}
