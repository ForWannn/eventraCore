<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('leave-requests.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:izin,cuti',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
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
}
