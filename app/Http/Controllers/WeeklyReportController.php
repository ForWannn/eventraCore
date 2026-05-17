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
                ->where('week_start_date', '<', $report->week_start_date->format('Y-m-d'))
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
        if (!Auth::user()->hasRole(['CEO', 'GM'])) abort(403);

        $now = Carbon::now();
        $weekStart = $request->query('week', $now->copy()->startOfWeek(Carbon::MONDAY)->format('Y-m-d'));

        // Ambil semua user bersama data laporan minggunya pada tanggal yang dipilih
        $users = User::whereDoesntHave('roles', function($q){
            $q->whereIn('name', ['CEO', 'Direktur']);
        })->with(['weeklyReports' => function($q) use ($weekStart) {
            $q->where('week_start_date', $weekStart);
        }, 'division'])->orderBy('name')->get();

        return view('weekly-reports.recap', compact('users', 'weekStart', 'now'));
    }

    // ── FITUR BARU: DETAIL REVIEW LAPORAN KARYAWAN ────────────────────
    public function showUserReport($userId, $weekStart)
    {
        if (!Auth::user()->hasRole(['CEO', 'GM'])) abort(403);

        $user = User::with('division')->findOrFail($userId);
        $report = WeeklyReport::where('user_id', $userId)
            ->where('week_start_date', $weekStart)
            ->with(['items', 'dailyLogs'])
            ->firstOrFail();

        return view('weekly-reports.show', compact('report', 'user'));
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
        
        $report->update(['notes' => $request->notes, 'status' => 'submitted', 'final_submitted_at' => now()]);
        return back()->with('success', 'Final Report berhasil dikirim.');
    }

    public function autoSaveLog(Request $request)
    {
        $desc = is_array($request->tasks) ? implode("\n", array_filter($request->tasks, fn($val) => !is_null($val) && trim($val) !== '')) : null;
        DailyLog::where('id', $request->log_id)->update(['description' => $desc]);
        return response()->json(['success' => true]);
    }
}