@extends('layouts.app')

@php
    $isDirector = Auth::user()->hasRole(['CEO', 'GM']);
@endphp

@section('title', $isDirector ? 'Review Laporan Karyawan' : 'Detail Laporan Mingguan')

@section('content')
<style>
    .section-box { border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; margin-bottom: 24px; background: var(--sidebar-bg); }
    .section-header { background: var(--hover-bg); padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--text-main); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; }
    .section-body { padding: 16px; }
    .input-read { width: 100%; border: none; border-bottom: 1px dotted var(--border-color); background: transparent; color: var(--text-main); padding: 8px 0; font-size: 13px; outline: none; }
    .days-grid { display: grid; grid-template-columns: repeat(5, 1fr); }
    .day-col { border-right: 1px solid var(--border-color); display: flex; flex-direction: column; min-height: 250px; }
    .day-col:last-child { border-right: none; }
    .day-header { padding: 12px 16px; background: var(--hover-bg); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 12px; }
    .day-date { font-size: 24px; font-weight: 700; color: var(--text-main); line-height: 1; }
    .day-name { font-size: 11px; color: var(--text-muted); text-transform: uppercase; }
    .status-toggle { display: flex; align-items: center; gap: 4px; margin-right: 8px; }
    .status-btn { width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); background: var(--hover-bg); color: var(--text-muted); }
    .status-btn.active-check { background: #dcfce7; color: #166534; border-color: #86efac; }
    .status-btn.active-cross { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }

    @media (max-width: 960px) {
        .show-header-row {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }
        .show-header-percentage {
            text-align: left !important;
        }
        .show-top-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        .show-objective-body {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 768px) {
        .days-grid {
            grid-template-columns: 1fr !important;
        }
        .day-col {
            border-right: none !important;
            border-bottom: 1px solid var(--border-color) !important;
            min-height: auto !important;
        }
        .day-col:last-child {
            border-bottom: none !important;
        }
        .day-header {
            padding: 10px 12px !important;
        }
        .day-col .section-body {
            padding: 12px !important;
        }
        h3{
            font-size: 16px !important;;
        }
        p,
        .day-name,
        .progress-badge{
            font-size: 10px !important;
        }
    }
</style>

<div style="margin-bottom: 20px;">
    @if($isDirector)
        <a href="{{ route('weekly.recap', ['week' => $report->week_start_date->format('Y-m-d')]) }}" class="btn-back">
            <i data-feather="arrow-left"></i>
            <span>Kembali ke Rekapitulasi</span>
        </a>
    @else
        <a href="{{ route('weekly.history') }}" class="btn-back">
            <i data-feather="arrow-left"></i>
            <span>Kembali ke Riwayat</span>
        </a>
    @endif
</div>

<div class="card">
    <div class="show-header-row" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
        <div>
            <h3 style="margin-bottom: 4px;">Weekly Report: {{ $user->name }}</h3>
            <p style="font-size: 13px; color: var(--text-muted);">Divisi: {{ optional($user->division)->name ?? '-' }} &nbsp;&bull;&nbsp; Minggu: {{ $report->week_start_date->format('d/m/Y') }}</p>
        </div>
        <div class="show-header-percentage" style="text-align: right; display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('weekly.export_pdf', [$user->id, $report->week_start_date->format('Y-m-d')]) }}" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; font-size: 14px; font-weight: 600; color: #003B7A; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#CBD5E1';" onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#E2E8F0';">
                <i data-feather="file-text" style="width: 16px; height: 16px; color: #003B7A;"></i>
                Ekspor PDF
            </a>
            <div style="font-size: 14px; font-weight: 700; background: #10b981; color: white; padding: 8px 16px; border-radius: 8px;" class="progress-badge">
                Progress: {{ $report->completion_percentage }}%
            </div>
        </div>
    </div>

    <div class="show-top-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
        <div class="section-box">
            <div class="section-header">Weekly Objective</div>
            <div class="section-body show-objective-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px;">
                @for($i = 0; $i < 10; $i++)
                    @php $item = $report->items->where('type', 'objective')->values()->get($i); @endphp
                    @if($item)
                    <div style="display: flex; align-items: center;">
                        <div class="status-toggle">
                            <div class="status-btn {{ $item->is_completed ? 'active-check' : 'active-cross' }}">
                                <i data-feather="{{ $item->is_completed ? 'check' : 'x' }}" style="width: 14px; height: 14px;"></i>
                            </div>
                        </div>
                        <input type="text" class="input-read" value="{{ $item->content }}" readonly>
                    </div>
                    @endif
                @endfor
            </div>
        </div>

        <div class="section-box">
            <div class="section-header">Deadline Bulan Ini</div>
            <div class="section-body" style="display: flex; flex-direction: column; gap: 8px;">
                @for($i = 0; $i < 5; $i++)
                    @php $item = $report->items->where('type', 'deadline')->values()->get($i); @endphp
                    @if($item)
                        <input type="text" class="input-read" value="{{ $item->content }}" readonly>
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <div class="section-box">
        <div class="days-grid">
            @foreach($report->dailyLogs as $log)
                @php 
                    $logDate = \Carbon\Carbon::parse($log->log_date); 
                    $tasks = array_filter(explode("\n", $log->description ?? ''));
                @endphp
                <div class="day-col">
                    <div class="day-header">
                        <div class="day-date">{{ $logDate->format('d') }}</div>
                        <div class="day-name"><div>{{ $logDate->format('l') }}</div></div>
                    </div>
                    <div class="section-body" style="font-size: 13px; color: var(--text-main); line-height: 1.6;">
                        @forelse($tasks as $t)
                            <div style="border-bottom: 1px dashed var(--border-color); padding: 6px 0;">&bull; {{ $t }}</div>
                        @empty
                            <span style="color: var(--text-muted); font-style: italic; font-size: 12px;">Tidak ada log kerja</span>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section-box">
        <div class="section-header">Notes Karyawan</div>
        <div class="section-body" style="font-size: 13px; color: var(--text-main); min-height: 50px;">
            {{ $report->notes ?? '-' }}
        </div>
    </div>
</div>
@endsection