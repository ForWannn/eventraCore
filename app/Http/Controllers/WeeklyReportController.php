<?php

namespace App\Http\Controllers;

use App\Models\WeeklyReport;
use App\Models\WeeklyItem;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WeeklyReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $thisMonday = $now->copy()->startOfWeek(Carbon::MONDAY);
        
        $currentReport = WeeklyReport::where('user_id', $user->id)
            ->where('week_start_date', $thisMonday->format('Y-m-d'))
            ->first();

        if ($currentReport && $currentReport->status === 'submitted') {
            $displayMonday = $thisMonday->copy()->addWeek();
        } else {
            $displayMonday = $thisMonday;
        }

        $report = WeeklyReport::firstOrCreate(
            ['user_id' => $user->id, 'week_start_date' => $displayMonday->format('Y-m-d')],
            ['status' => 'draft']
        );

        if ($report->items()->where('type', 'deadline')->count() == 0) {
            $previousReport = WeeklyReport::where('user_id', $user->id)
                ->where('week_start_date', '<', $report->week_start_date->toDateString())
                ->orderBy('week_start_date', 'desc')
                ->first();

            if ($previousReport) {
                $prevMonday = Carbon::parse($previousReport->week_start_date);
                $currMonday = Carbon::parse($report->week_start_date);
                
                if ($prevMonday->format('Y-m') === $currMonday->format('Y-m')) {
                    foreach ($previousReport->items()->where('type', 'deadline')->get() as $dl) {
                        WeeklyItem::create([
                            'weekly_report_id' => $report->id,
                            'type'             => 'deadline',
                            'content'          => $dl->content,
                            'is_completed'     => $dl->is_completed
                        ]);
                    }
                }
            }
        }

        for ($i = 0; $i < 5; $i++) {
            DailyLog::firstOrCreate([
                'weekly_report_id' => $report->id,
                'log_date' => $displayMonday->copy()->addDays($i)->format('Y-m-d')
            ]);
        }

        $report->load(['items', 'dailyLogs']);
        return view('weekly-reports.index', compact('report', 'now'));
    }

    // ── FITUR BARU: HALAMAN REKAP UNTUK CEO & GM ──────────────────────
    public function recap(Request $request)
    {
        if (!Auth::user()->hasRole(['CEO', 'GM']) && !Auth::user()->can('rekap_weekly')) abort(403);

        $now = Carbon::now();
        $weekStart = $request->query('week', $now->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'));
        $search = $request->query('search');
        $divisionId = $request->query('division_id');
        $statusFilter = $request->query('status', 'all');

        // Ambil semua user bersama data laporan minggunya pada tanggal yang dipilih
        $users = User::whereDoesntHave('roles', function($q){
            $q->whereIn('name', ['CEO', 'Direktur']);
        })->with(['weeklyReports' => function($q) use ($weekStart) {
            $q->where('week_start_date', $weekStart);
        }, 'division'])->orderBy('name')->get();

        // Map and compute status details for all users
        $usersData = $users->map(function ($u) {
            $userReport = $u->weeklyReports->first();
            
            $planStatus = 'belum';
            $finalStatus = 'belum';
            $completion = 0;
            
            if ($userReport) {
                if ($userReport->plan_submitted_at) {
                    $planStatus = $userReport->is_late_plan ? 'terlambat' : 'terkirim';
                }
                
                if ($userReport->status === 'submitted') {
                    $finalStatus = 'selesai';
                    $completion = $userReport->completion_percentage;
                } elseif ($userReport->status === 'draft') {
                    $finalStatus = 'draft';
                }
            }
            
            return [
                'user' => $u,
                'userReport' => $userReport,
                'plan_status' => $planStatus,
                'final_status' => $finalStatus,
                'completion' => $completion,
            ];
        });

        // Compute summary statistics BEFORE filters are applied
        $totalStaff = $usersData->count();
        $planSubmittedCount = $usersData->filter(fn($item) => $item['plan_status'] !== 'belum')->count();
        $planLateCount = $usersData->filter(fn($item) => $item['plan_status'] === 'terlambat')->count();
        $finalSubmittedCount = $usersData->filter(fn($item) => $item['final_status'] === 'selesai')->count();
        
        $submittedReports = $usersData->filter(fn($item) => $item['final_status'] === 'selesai');
        $averageCompletion = $submittedReports->count() > 0 ? round($submittedReports->avg('completion')) : 0;

        $planPct = $totalStaff > 0 ? round(($planSubmittedCount / $totalStaff) * 100, 1) : 0;
        $planLatePct = $planSubmittedCount > 0 ? round(($planLateCount / $planSubmittedCount) * 100, 1) : 0;
        $finalPct = $totalStaff > 0 ? round(($finalSubmittedCount / $totalStaff) * 100, 1) : 0;

        // Apply filters
        if ($search) {
            $usersData = $usersData->filter(function ($item) use ($search) {
                return strpos(strtolower($item['user']->name), strtolower($search)) !== false;
            });
        }

        if ($divisionId && $divisionId !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($divisionId) {
                return (string)$item['user']->division_id === (string)$divisionId;
            });
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($statusFilter) {
                if ($statusFilter === 'belum_setor_plan') {
                    return $item['plan_status'] === 'belum';
                } elseif ($statusFilter === 'plan_terkirim') {
                    return $item['plan_status'] === 'terkirim' || $item['plan_status'] === 'terlambat';
                } elseif ($statusFilter === 'plan_terlambat') {
                    return $item['plan_status'] === 'terlambat';
                } elseif ($statusFilter === 'laporan_draft') {
                    return $item['final_status'] === 'draft';
                } elseif ($statusFilter === 'laporan_selesai') {
                    return $item['final_status'] === 'selesai';
                }
                return true;
            });
        }

        // Paginate results
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $currentItems = array_slice($usersData->all(), ($currentPage - 1) * $perPage, $perPage);
        
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $usersData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $divisions = \App\Models\Division::orderBy('name', 'asc')->get();

        return view('weekly-reports.recap', [
            'users' => $paginatedUsers,
            'weekStart' => $weekStart,
            'now' => $now,
            'divisions' => $divisions,
            'totalStaff' => $totalStaff,
            'planSubmittedCount' => $planSubmittedCount,
            'planLateCount' => $planLateCount,
            'finalSubmittedCount' => $finalSubmittedCount,
            'averageCompletion' => $averageCompletion,
            'stats' => [
                'plan_pct' => $planPct,
                'plan_late_pct' => $planLatePct,
                'final_pct' => $finalPct,
            ],
            'filters' => [
                'search' => $search,
                'division_id' => $divisionId,
                'status' => $statusFilter,
                'per_page' => $perPage,
            ]
        ]);
    }

    public function exportRecap(Request $request)
    {
        if (!Auth::user()->hasRole(['CEO', 'GM']) && !Auth::user()->can('rekap_weekly')) abort(403);

        $now = Carbon::now();
        $weekStart = $request->query('week', $now->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'));
        $search = $request->query('search');
        $divisionId = $request->query('division_id');
        $statusFilter = $request->query('status', 'all');

        $users = User::whereDoesntHave('roles', function($q){
            $q->whereIn('name', ['CEO', 'Direktur']);
        })->with(['weeklyReports' => function($q) use ($weekStart) {
            $q->where('week_start_date', $weekStart);
        }, 'division'])->orderBy('name')->get();

        $usersData = $users->map(function ($u) {
            $userReport = $u->weeklyReports->first();
            
            $planStatus = 'belum';
            $finalStatus = 'belum';
            $completion = 0;
            
            if ($userReport) {
                if ($userReport->plan_submitted_at) {
                    $planStatus = $userReport->is_late_plan ? 'terlambat' : 'terkirim';
                }
                
                if ($userReport->status === 'submitted') {
                    $finalStatus = 'selesai';
                    $completion = $userReport->completion_percentage;
                } elseif ($userReport->status === 'draft') {
                    $finalStatus = 'draft';
                }
            }
            
            return [
                'user' => $u,
                'name' => $u->name,
                'nik' => $u->nik ?? '-',
                'division' => $u->division->name ?? 'Tanpa Divisi',
                'plan_status' => $planStatus,
                'final_status' => $finalStatus,
                'completion' => $completion,
            ];
        });

        // Apply filters
        if ($search) {
            $usersData = $usersData->filter(function ($item) use ($search) {
                return strpos(strtolower($item['name']), strtolower($search)) !== false;
            });
        }

        if ($divisionId && $divisionId !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($divisionId) {
                return (string)$item['user']->division_id === (string)$divisionId;
            });
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($statusFilter) {
                if ($statusFilter === 'belum_setor_plan') {
                    return $item['plan_status'] === 'belum';
                } elseif ($statusFilter === 'plan_terkirim') {
                    return $item['plan_status'] === 'terkirim' || $item['plan_status'] === 'terlambat';
                } elseif ($statusFilter === 'plan_terlambat') {
                    return $item['plan_status'] === 'terlambat';
                } elseif ($statusFilter === 'laporan_draft') {
                    return $item['final_status'] === 'draft';
                } elseif ($statusFilter === 'laporan_selesai') {
                    return $item['final_status'] === 'selesai';
                }
                return true;
            });
        }

        $fileName = 'rekap_weekly_report_' . $weekStart . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($usersData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Nama Karyawan', 'NIK', 'Departemen', 'Status Perencanaan', 'Status Laporan Akhir', 'Tingkat Penyelesaian']);

            foreach ($usersData as $row) {
                $planLabel = 'Belum Setor';
                if ($row['plan_status'] === 'terkirim') $planLabel = 'Terkirim';
                elseif ($row['plan_status'] === 'terlambat') $planLabel = 'Terkirim (Terlambat)';

                $finalLabel = 'Proses / Draft';
                if ($row['final_status'] === 'selesai') $finalLabel = 'Selesai diserahkan';

                fputcsv($file, [
                    $row['name'],
                    $row['nik'],
                    $row['division'],
                    $planLabel,
                    $finalLabel,
                    $row['final_status'] === 'selesai' ? $row['completion'] . '%' : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $isDirector = $user->hasRole(['CEO', 'GM']) || $user->can('weekly_history');
        if ($user->hasRole(['Employee', 'Intern'])) {
            $isDirector = false;
        }
        
        $search = $request->query('search');
        $status = $request->query('status');
        $month = $request->query('month');
        $year = $request->query('year');

        // Base query
        if ($isDirector) {
            $query = WeeklyReport::with(['user', 'user.division', 'items', 'dailyLogs'])
                ->where('status', 'submitted');
        } else {
            $query = WeeklyReport::where('user_id', $user->id)->with(['items', 'dailyLogs']);
        }

        // Apply Search Filter (Search in user name/division for director, or objective items content for all)
        if ($search) {
            $query->where(function($q) use ($search, $isDirector) {
                if ($isDirector) {
                    $q->whereHas('user', function($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    })->orWhereHas('items', function($sub) use ($search) {
                        $sub->where('content', 'like', "%{$search}%");
                    })->orWhereHas('user.division', function($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
                } else {
                    $q->whereHas('items', function($sub) use ($search) {
                        $sub->where('content', 'like', "%{$search}%");
                    });
                }
            });
        }

        // Apply Status Filter (only relevant for employees, since directors only view submitted reports)
        if ($status) {
            $query->where('status', $status);
        }

        // Apply Month Filter
        if ($month) {
            $query->whereMonth('week_start_date', $month);
        }

        // Apply Year Filter
        if ($year) {
            $query->whereYear('week_start_date', $year);
        }

        // Calculate Stats based on the filtered query (before pagination)
        $statsQuery = clone $query;
        
        $totalSubmitted = (clone $statsQuery)->where('status', 'submitted')->count();
        $totalLate = (clone $statsQuery)->where('status', 'submitted')->where('is_late_plan', true)->count();
        $totalOnTime = (clone $statsQuery)->where('status', 'submitted')->where('is_late_plan', false)->count();
        $averageCompletion = round((clone $statsQuery)->avg('completion_percentage') ?? 0);

        // Fetch reports with pagination (5 per page to match mockup)
        if ($isDirector) {
            $reports = $query->orderBy('week_start_date', 'desc')
                ->orderBy('user_id')
                ->paginate(5)
                ->withQueryString();
        } else {
            $reports = $query->orderBy('week_start_date', 'desc')
                ->paginate(5)
                ->withQueryString();
        }

        // Available years for dropdown
        $availableYears = WeeklyReport::pluck('week_start_date')
            ->map(function($date) {
                return \Carbon\Carbon::parse($date)->year;
            })
            ->unique()
            ->sortDesc()
            ->values();
        
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        return view('weekly-reports.history', compact(
            'reports',
            'isDirector',
            'search',
            'status',
            'month',
            'year',
            'totalSubmitted',
            'totalLate',
            'totalOnTime',
            'averageCompletion',
            'availableYears'
        ));
    }

    public function showUserReport($userId, $weekStart)
    {
        $currentUser = Auth::user();
        if ($currentUser->hasRole(['Employee', 'Intern']) && $currentUser->id != $userId) {
            abort(403);
        }
        if (!$currentUser->hasRole(['CEO', 'GM']) && !$currentUser->can('rekap_weekly') && !$currentUser->can('weekly_history') && $currentUser->id != $userId) {
            abort(403);
        }

        $user = User::with('division')->findOrFail($userId);
        $report = WeeklyReport::where('user_id', $userId)
            ->whereDate('week_start_date', $weekStart)
            ->with(['items', 'dailyLogs'])
            ->firstOrFail();

        return view('weekly-reports.show', compact('report', 'user'));
    }

    public function exportPdf($userId, $weekStart)
    {
        $currentUser = Auth::user();
        if ($currentUser->hasRole(['Employee', 'Intern']) && $currentUser->id != $userId) {
            abort(403);
        }
        if (!$currentUser->hasRole(['CEO', 'GM']) && !$currentUser->can('rekap_weekly') && !$currentUser->can('weekly_history') && $currentUser->id != $userId) {
            abort(403);
        }

        $user = User::with('division')->findOrFail($userId);
        $report = WeeklyReport::where('user_id', $userId)
            ->whereDate('week_start_date', $weekStart)
            ->with(['items', 'dailyLogs'])
            ->firstOrFail();

        $weekStartDate = Carbon::parse($report->week_start_date);
        $weekEndDate = $weekStartDate->copy()->addDays(4); // Monday to Friday

        // Formatted range in Indonesian
        $dateRangeString = $weekStartDate->locale('id')->translatedFormat('d F Y') . ' - ' . $weekEndDate->locale('id')->translatedFormat('d F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('weekly-reports.pdf', compact('report', 'user', 'dateRangeString'));

        $filename = 'weekly_report_' . strtolower(str_replace(' ', '_', $user->name)) . '_' . $report->week_start_date->format('Y-m-d') . '.pdf';
        
        return $pdf->stream($filename);
    }

    public function updatePlan(Request $request, WeeklyReport $report)
    {
        $now = now();
        $deadline = Carbon::parse($report->week_start_date)->addHours(9);
        $isLate = $now->greaterThan($deadline);

        $report->items()->delete();
        
        if ($request->has('objectives')) {
            foreach ($request->objectives as $content) {
                if ($content) WeeklyItem::create(['weekly_report_id' => $report->id, 'type' => 'objective', 'content' => $content]);
            }
        }
        if ($request->has('deadlines')) {
            foreach ($request->deadlines as $content) {
                if ($content) WeeklyItem::create(['weekly_report_id' => $report->id, 'type' => 'deadline', 'content' => $content]);
            }
        }

        $report->update(['plan_submitted_at' => $now, 'is_late_plan' => $isLate]);
        return back()->with('success', 'Plan & Deadline berhasil disimpan.');
    }

    public function submitFinal(Request $request, WeeklyReport $report)
    {
        if ($request->has('logs')) {
            foreach ($request->logs as $logId => $tasks) {
                $desc = is_array($tasks) ? implode("\n", array_filter($tasks, fn($val) => !is_null($val) && trim($val) !== '')) : $tasks;
                DailyLog::where('id', $logId)->update(['description' => $desc]);
            }
        }
        if ($request->has('item_status')) {
            foreach ($request->item_status as $itemId => $status) {
                WeeklyItem::where('id', $itemId)->update(['is_completed' => $status]);
            }
        }
        
        $total = $report->items()->where('type', 'objective')->count();
        $completed = $report->items()->where('type', 'objective')->where('is_completed', true)->count();
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        $report->update([
            'notes' => $request->notes, 
            'status' => 'submitted', 
            'final_submitted_at' => now(),
            'completion_percentage' => $percentage
        ]);
        return back()->with('success', 'Final Report berhasil dikirim.');
    }

    public function autoSaveLog(Request $request)
    {
        $desc = is_array($request->tasks) ? implode("\n", array_filter($request->tasks, fn($val) => !is_null($val) && trim($val) !== '')) : null;
        DailyLog::where('id', $request->log_id)->update(['description' => $desc]);
        return response()->json(['success' => true]);
    }
}