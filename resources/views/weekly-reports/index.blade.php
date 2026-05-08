@extends('layouts.app')

@section('title', 'Weekly Report')

@section('content')
<style>
    /* Styling Modern Minimalis eventraCore */
    .section-box {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        background: var(--sidebar-bg);
    }
    
    .section-header {
        background: var(--hover-bg);
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
    }
    
    .section-body {
        padding: 16px;
    }
    
    /* Input Styling */
    .input-line {
        width: 100%;
        border: none;
        border-bottom: 1px dashed var(--border-color);
        background: transparent;
        color: var(--text-main);
        padding: 8px 0;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s;
    }
    
    .input-line:focus {
        border-bottom: 1px solid var(--text-muted);
    }
    
    .input-line:read-only {
        color: var(--text-muted);
        border-bottom-style: dotted;
    }

    /* Daily Logs Grid */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
    }
    
    .day-col {
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        min-height: 350px;
    }
    
    .day-col:last-child { border-right: none; }
    
    @media (max-width: 1024px) {
        .days-grid { grid-template-columns: 1fr; }
        .day-col { border-right: none; border-bottom: 1px solid var(--border-color); min-height: auto; }
    }
    
    .day-header {
        padding: 12px 16px;
        background: var(--hover-bg);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .day-date {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
    }
    
    .day-name {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }
    
    .day-body {
        flex: 1;
        padding: 12px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Task Row Dynamic Styles */
    .task-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }

    .btn-add-task {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 8px;
        background: transparent;
        border: 1px dashed var(--border-color);
        color: var(--text-muted);
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add-task:hover {
        background: var(--hover-bg);
        color: var(--text-main);
        border-color: var(--text-muted);
    }

    /* Primary Buttons */
    .btn-primary {
        display: inline-block;
        padding: 10px 20px;
        background: var(--primary);
        color: var(--primary-text);
        border: none;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: opacity 0.2s;
        text-decoration: none;
    }
    
    .btn-primary:hover { opacity: 0.9; }

    .btn-success {
        display: inline-block;
        padding: 12px 32px;
        background: #000000ff;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    
    .btn-success:hover { opacity: 0.9; }

    .alert-late {
        /* background: #fff1f2; */
        color: #e11d48;
        /* border: 1px solid #fda4af; */
        /* padding: 10px 16px; */
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* [data-theme="dark"] .alert-late { background: rgba(225, 29, 72, 0.1); border-color: rgba(225, 29, 72, 0.2); } */

    /* Style untuk Check/Cross Toggle */
    .status-toggle {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-right: 8px;
    }

    .status-btn {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 1px solid var(--border-color);
        background: var(--hover-bg);
        color: var(--text-muted);
        transition: all 0.2s;
    }

    .status-btn.active-check { 
        background: #dcfce7; 
        color: #166534; 
        border-color: #86efac; 
    }
    
    .status-btn.active-cross { 
        background: #fee2e2; 
        color: #b91c1c; 
        border-color: #fca5a5; 
    }

    .status-btn:hover:not(.disabled) {
        border-color: var(--text-muted);
    }
    
    .status-btn.disabled {
        cursor: default;
        opacity: 0.7;
    }

    .item-row {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
    }
</style>

<div class="card">
    @php
        $isFinalPhase = !$now->isWeekend() && $now->format('H:i') >= '17:00';
    @endphp
    @if($report->is_late_plan)
        <div class="alert-late">
            <i data-feather="clock" style="width: 14px; height: 14px;"></i>
            PENGUMPULAN PLAN TERLAMBAT
        </div>
    @endif

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="margin-bottom: 4px;">Weekly Schedule Planner</h3>
            <p style="font-size: 13px; color: var(--text-muted);">
                Minggu: <strong>{{ \Carbon\Carbon::parse($report->week_start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($report->week_start_date)->addDays(4)->format('d M Y') }}</strong>
            </p>
        </div>
        
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 11px; padding: 6px 12px; border-radius: 20px; background: var(--hover-bg); border: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600;">
                {{ strtoupper($report->status) }}
            </div>
            
            @if(!$report->plan_submitted_at && $report->status !== 'submitted')
                <button type="submit" form="planForm" class="btn-primary">
                    Simpan Plan & Deadline
                </button>
            @elseif($report->plan_submitted_at && $report->status !== 'submitted')
                <div style="font-size: 11px; color: #10b981; font-weight: 600; display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                    <i data-feather="lock" style="width: 12px; height: 12px;"></i> Plan Disimpan
                </div>
            @endif
        </div>
    </div>

    <form id="planForm" action="{{ route('weekly.plan', $report->id) }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;" class="top-responsive">
            <style>@media (max-width: 768px) { .top-responsive { grid-template-columns: 1fr !important; } }</style>
            
            <div class="section-box" style="margin-bottom: 0;">
                <div class="section-header">
                    <span>Weekly Objective</span>
                </div>
                <div class="section-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px;">
                    @for($i = 0; $i < 10; $i++)
                        @php $item = $report->items->where('type', 'objective')->values()->get($i); @endphp
                        <div class="item-row">
                            @if($item && $item->content)
                                <div class="status-toggle">
                                    <input type="hidden" name="item_status[{{ $item->id }}]" value="{{ $item->is_completed ? '1' : '0' }}" id="status-{{ $item->id }}">
                                    
                                    <div class="status-btn {{ $item->is_completed ? 'active-check' : '' }} {{ !$isFinalPhase ? 'disabled' : '' }}" 
                                        onclick="{{ $isFinalPhase ? 'toggleStatus('.$item->id.', 1)' : '' }}" title="Selesai">
                                        <i data-feather="check" style="width: 14px; height: 14px;"></i>
                                    </div>
                                    <div class="status-btn {{ ($item && !$item->is_completed && $report->status === 'submitted') ? 'active-cross' : '' }} {{ !$isFinalPhase ? 'disabled' : '' }}" 
                                        onclick="{{ $isFinalPhase ? 'toggleStatus('.$item->id.', 0)' : '' }}" title="Tidak Selesai">
                                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                                    </div>
                                </div>
                            @endif
                            <input type="text" name="objectives[]" value="{{ $item->content ?? '' }}" 
                                class="input-line" placeholder="..." {{ $report->plan_submitted_at ? 'readonly' : '' }}>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="section-box" style="margin-bottom: 0;">
                <div class="section-header">
                    <span>Deadline {{ \Carbon\Carbon::parse($report->week_start_date)->format('F') }}</span>
                </div>
                <div class="section-body" style="display: flex; flex-direction: column; gap: 12px;">
    @for($i = 0; $i < 5; $i++)
        @php $item = $report->items->where('type', 'deadline')->values()->get($i); @endphp
        <div class="item-row">
            @if($item && $item->content)
                <div class="status-toggle">
                    <input type="hidden" name="item_status[{{ $item->id }}]" value="{{ $item->is_completed ? '1' : '0' }}" id="status-{{ $item->id }}">
                    
                    <div class="status-btn {{ $item->is_completed ? 'active-check' : '' }} {{ !$isFinalPhase ? 'disabled' : '' }}" 
                        onclick="{{ $isFinalPhase ? 'toggleStatus('.$item->id.', 1)' : '' }}" title="Selesai">
                        <i data-feather="check" style="width: 14px; height: 14px;"></i>
                    </div>
                    <div class="status-btn {{ ($item && !$item->is_completed && $report->status === 'submitted') ? 'active-cross' : '' }} {{ !$isFinalPhase ? 'disabled' : '' }}" 
                        onclick="{{ $isFinalPhase ? 'toggleStatus('.$item->id.', 0)' : '' }}" title="Tidak Selesai">
                        <i data-feather="x" style="width: 14px; height: 14px;"></i>
                    </div>
                </div>
            @endif
            <input type="text" name="deadlines[]" value="{{ $item->content ?? '' }}" 
                class="input-line" placeholder="..." {{ $report->plan_submitted_at ? 'readonly' : '' }}>
        </div>
    @endfor
</div>
            </div>
        </div>
    </form>

    <form action="{{ route('weekly.final', $report->id) }}" method="POST">
        @csrf
        <div class="section-box">
            <div class="days-grid">
                @foreach($report->dailyLogs as $log)
                    @php 
                        $logDate = \Carbon\Carbon::parse($log->log_date); 
                        $savedTasks = $log->description ? explode("\n", $log->description) : [];
                        $tasks = array_pad($savedTasks, 5, '');
                    @endphp
                    <div class="day-col">
                        <div class="day-header">
                            <div class="day-date">{{ $logDate->format('d') }}</div>
                            <div class="day-name">
                                <div>{{ $logDate->format('l') }}</div>
                                <div style="font-weight: 500;">{{ $logDate->format('M Y') }}</div>
                            </div>
                        </div>
                        <div class="day-body">
                            <div class="task-container" id="task-container-{{ $log->id }}">
                                @foreach($tasks as $task)
                                    <input type="text" name="logs[{{ $log->id }}][]" class="input-line" 
                                           value="{{ $task }}" placeholder="" 
                                           {{ $report->status === 'submitted' ? 'readonly' : '' }}>
                                @endforeach
                            </div>
                            
                            @if($report->status !== 'submitted')
                                <button type="button" class="btn-add-task" onclick="addTaskRow({{ $log->id }})">
                                    <i data-feather="plus" style="width: 12px; height: 12px;"></i> Tambah
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="section-box">
            <div class="section-header">Catatan / Kendala (Opsional)</div>
            <textarea name="notes" style="width: 100%; height: 80px; border: none; background: transparent; color: var(--text-main); font-size: 13px; padding: 16px; outline: none; resize: none;" {{ $report->status === 'submitted' ? 'readonly' : '' }}>{{ $report->notes }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 24px;">
            @if($report->status !== 'submitted')
                <button type="submit" class="btn-primary">Submit Final Report</button>
            @else
                <div style="color: #10b981; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="check-circle" style="width: 16px; height: 16px;"></i>
                    Laporan disubmit pada {{ \Carbon\Carbon::parse($report->final_submitted_at)->format('d/m/Y H:i') }}
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    function addTaskRow(logId) {
        const container = document.getElementById('task-container-' + logId);
        const input = document.createElement('input');
        input.type = 'text';
        input.name = `logs[${logId}][]`;
        input.className = 'input-line';
        input.placeholder = '';
        container.appendChild(input);
        input.focus();
    }
    function toggleStatus(itemId, isCompleted) {
    const input = document.getElementById('status-' + itemId);
    const parent = input.parentElement;
    const btns = parent.querySelectorAll('.status-btn');
    
    // Set value ke hidden input
    input.value = isCompleted;
    
    // Reset classes
    btns[0].classList.remove('active-check');
    btns[1].classList.remove('active-cross');
    
    // Aktifkan salah satu
    if (isCompleted === 1) {
        btns[0].classList.add('active-check');
    } else {
        btns[1].classList.add('active-cross');
    }
}
</script>
@endsection