<?php

namespace App\Http\Controllers;

use App\Models\WeeklyReport;
use App\Models\WeeklyItem;
use App\Models\DailyLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WeeklyReportController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $now = Carbon::now();
    
    // 1. Tentukan Senin minggu ini
    $thisMonday = $now->copy()->startOfWeek(Carbon::MONDAY);
    
    // 2. Cek apakah laporan minggu ini sudah di-submit
    $currentReport = WeeklyReport::where('user_id', $user->id)
        ->where('week_start_date', $thisMonday->format('Y-m-d'))
        ->first();

    // 3. Jika sudah submit FINAL, maka yang ditampilkan adalah laporan MINGGU DEPAN
    if ($currentReport && $currentReport->status === 'submitted') {
        $displayMonday = $thisMonday->copy()->addWeek();
    } else {
        $displayMonday = $thisMonday;
    }

    $report = WeeklyReport::firstOrCreate(
        ['user_id' => $user->id, 'week_start_date' => $displayMonday->format('Y-m-d')],
        ['status' => 'draft']
    );

    // Inisialisasi daily logs jika belum ada
    for ($i = 0; $i < 5; $i++) {
        DailyLog::firstOrCreate([
            'weekly_report_id' => $report->id,
            'log_date' => $displayMonday->copy()->addDays($i)->format('Y-m-d')
        ]);
    }

    $report->load(['items', 'dailyLogs']);
    return view('weekly-reports.index', compact('report', 'now'));
}

public function updatePlan(Request $request, WeeklyReport $report)
{
    $now = now();
    
    // 1. Kunci jika sudah pernah submit plan minggu ini
    if ($report->plan_submitted_at) {
        return back()->with('error', 'Plan minggu ini sudah dikunci dan tidak dapat diubah.');
    }

    $deadline = \Carbon\Carbon::parse($report->week_start_date)->addHours(9);
    $isLate = $now->greaterThan($deadline);

    $report->items()->delete();
    
    foreach ($request->objectives as $content) {
        if ($content) \App\Models\WeeklyItem::create(['weekly_report_id' => $report->id, 'type' => 'objective', 'content' => $content]);
    }
    foreach ($request->deadlines as $content) {
        if ($content) \App\Models\WeeklyItem::create(['weekly_report_id' => $report->id, 'type' => 'deadline', 'content' => $content]);
    }

    $report->update([
        'plan_submitted_at' => $now,
        'is_late_plan' => $isLate
    ]);

    return back()->with('success', 'Weekly Plan berhasil dikunci dan disimpan.');
}

public function submitFinal(Request $request, WeeklyReport $report)
{
    $now = now();

    // 1. Tutup akses jika hari Sabtu atau Minggu
    if ($now->isWeekend()) {
        return back()->with('error', 'Akses pengiriman laporan ditutup pada hari Sabtu dan Minggu.');
    }

    // 2. Logika jam (Jumat/Kamis setelah jam 17:00)
    $isFridayHoliday = false; // Sesuaikan jika ada tabel holiday
    $deadlineDay = $isFridayHoliday ? $now->isThursday() : $now->isFriday();

    if (!$deadlineDay || $now->format('H:i') < '17:00') {
        return back()->with('error', 'Laporan final hanya bisa dikirim pada hari Jumat setelah pukul 17:00.');
    }

    if ($request->has('item_status')) {
        foreach ($request->item_status as $itemId => $status) {
            \App\Models\WeeklyItem::where('id', $itemId)
                ->where('weekly_report_id', $report->id)
                ->update(['is_completed' => $status]);
        }
    }
    
    foreach ($request->logs as $logId => $tasks) {
        $desc = is_array($tasks) 
            ? implode("\n", array_filter($tasks, fn($val) => !is_null($val) && trim($val) !== '')) 
            : $tasks;
        \App\Models\DailyLog::where('id', $logId)->update(['description' => $desc]);
    }
    
    $report->update([
        'notes' => $request->notes,
        'status' => 'submitted',
        'final_submitted_at' => $now
    ]);

    return back()->with('success', 'Final Report berhasil terkirim.');
}
}