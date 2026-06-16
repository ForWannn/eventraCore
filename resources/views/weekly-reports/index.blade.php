@extends('layouts.app')

@section('title', 'Weekly Report')

@section('content')
<style>
    .section-box {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 24px;
        background: var(--sidebar-bg);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .section-header {
        /* background: var(--hover-bg); */
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
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
        cursor: not-allowed;
    }

    /* Daily Logs Grid */
    .days-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }
    
    @media (max-width: 1200px) {
        .days-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .days-grid { grid-template-columns: 1fr; }
    }
    
    .day-col {
        background: var(--sidebar-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        min-height: 380px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .day-col:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    }
    
    .day-header {
        padding: 16px;
        background: var(--hover-bg);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .day-date {
        font-size: 32px;
        font-weight: 700;
        color: #2563eb;
        line-height: 0.9;
    }
    
    .day-meta {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .day-name {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-main);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.1;
    }

    .day-month-year {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
        line-height: 1.1;
        margin-top: 2px;
    }
    
    .day-body {
        flex: 1;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Task Row Dynamic Styles */
    .task-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .daily-task-row {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    .btn-add-task {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        padding: 10px;
        background: transparent;
        border: 1px dashed var(--border-color);
        color: #2563eb;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 16px;
    }

    .btn-add-task:hover {
        background: var(--hover-bg);
        border-color: #2563eb;
    }

    /* Primary Buttons */
    .btn-save-plan {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    
    .btn-save-plan:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    }

    .btn-submit-final {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: #0f172a;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }
    
    .btn-submit-final:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
    }

    .alert-late {
        background: rgba(225, 29, 72, 0.05);
        color: #e11d48;
        border: 1px solid rgba(225, 29, 72, 0.15);
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .checkbox-btn {
        transition: all 0.2s;
    }
    .checkbox-btn:hover {
        transform: scale(1.05);
    }
    .checkbox-btn.checked {
        border-color: #10b981 !important;
        background-color: #10b981 !important;
    }
    .checkbox-btn.crossed {
        border-color: #ef4444 !important;
        background-color: #ef4444 !important;
    }

    .objective-row, .deadline-row {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    @media (max-width: 960px) {
        .top-responsive {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 768px) {
        .day-col {
            min-height: auto !important;
        }
        .weekly-header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
            margin-bottom: 20px !important;
        }
        .weekly-header-title-block {
            gap: 12px !important;
        }
        .weekly-header-icon-box {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
        }
        .weekly-header-icon-box svg {
            width: 18px !important;
            height: 18px !important;
        }
        .weekly-header-title {
            font-size: 16px !important;
        }
        .weekly-header-subtitle {
            font-size: 10px !important;
            margin-top: 2px !important;
        }
        .weekly-header-actions {
            width: 100% !important;
            justify-content: space-between !important;
        }
        .weekly-header-actions button, .weekly-header-actions div {
            flex: 1 !important;
            text-align: center !important;
            justify-content: center !important;
        }
        
        .section-box {
            margin-bottom: 16px !important;
            border-radius: 12px !important;
        }
        .section-header {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }
        .section-body {
            padding: 12px !important;
        }
        
        .day-col {
            margin-bottom: 16px !important;
            border-radius: 12px !important;
        }
        .day-header {
            padding: 12px !important;
            gap: 10px !important;
        }
        .day-date {
            font-size: 24px !important;
        }
        .day-body {
            padding: 12px !important;
        }
        .input-line {
            font-size: 12px !important;
            padding: 6px 0 !important;
        }
        .btn-add-task {
            padding: 8px !important;
            margin-top: 12px !important;
            border-radius: 8px !important;
        }
        .btn-save-plan {
            /* padding: 5px 10px !important; */
            font-size: 10px !important;
            border-radius: 8px !important;
        }
        .btn-submit-final {
            width: 100% !important;
            padding: 10px 24px !important;
            font-size: 13px !important;
            border-radius: 8px !important;
            justify-content: center !important;
        }
        .weekly-footer-actions {
            margin-top: 16px !important;
            margin-bottom: 24px !important;
        }
        .objectives-responsive {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div>
    @php
        $isFinalPhase = true;
        // $isFinalPhase = !$now->isWeekend() && $now->format('H:i') >= '17:00';
    @endphp
    
    @if($report->is_late_plan)
        <!-- <div class="alert-late">
            <i data-feather="clock" style="width: 14px; height: 14px;"></i>
            PENGUMPULAN PLAN TERLAMBAT
        </div> -->
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
    @if($report->status === 'submitted')
        <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 14px 20px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i data-feather="check-circle" style="width: 16px; height: 16px;"></i>
                Laporan Mingguan resmi diserahkan pada {{ \Carbon\Carbon::parse($report->final_submitted_at)->format('d/m/Y H:i') }}
            </div>
            <div style="font-weight: 700; background: #166534; color: white; padding: 4px 12px; border-radius: 8px;">
                Penyelesaian: {{ $report->completion_percentage }}%
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="weekly-header-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px;">
        <div class="weekly-header-title-block" style="display: flex; align-items: center; gap: 16px;">
            <div class="weekly-header-icon-box" style="width: 48px; height: 48px; border: 1px solid #dbeafe; border-radius: 12px; background: rgba(37, 99, 235, 0.08); display: flex; align-items: center; justify-content: center;">
                <i data-feather="calendar" style="width: 24px; height: 24px; color: #2563eb;"></i>
            </div>
            <div>
                <h2 class="weekly-header-title" style="font-size: 22px; font-weight: 700; color: var(--text-main); margin: 0; letter-spacing: -0.5px;">Weekly Report Planner</h2>
                <p class="weekly-header-subtitle" style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0; display: flex; align-items: center; gap: 6px; font-weight: 500;">
                    <i data-feather="calendar" style="width: 14px; height: 14px; color: var(--text-muted);"></i>
                    Minggu, {{ \Carbon\Carbon::parse($report->week_start_date)->locale('id')->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($report->week_start_date)->addDays(4)->locale('id')->translatedFormat('d M Y') }}
                </p>
                @php
                    $timestamps = [];
                    if ($report->plan_saved_at) {
                        $timestamps[] = 'dibuat pada ' . $report->plan_saved_at->format('d/m/Y H:i');
                    }
                    if ($report->plan_submitted_at) {
                        $timestamps[] = 'dikirim pada ' . $report->plan_submitted_at->format('d/m/Y H:i');
                    }
                    if ($report->final_submitted_at) {
                        $timestamps[] = 'di submit pada ' . $report->final_submitted_at->format('d/m/Y H:i');
                    }
                @endphp
                @if(!empty($timestamps))
                    <p style="font-size: 11px; color: var(--text-muted); margin: 4px 0 0 0; font-weight: 500;">
                        {!! implode(' &nbsp;&bull;&nbsp; ', $timestamps) !!}
                    </p>
                @endif
            </div>
        </div>

        <div class="weekly-header-actions" style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 11px; padding: 6px 14px; border-radius: 8px; background: var(--hover-bg); border: 1px solid var(--border-color); color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                {{ $report->status }}
            </div>
            
            @if(is_null($report->plan_submitted_at))
                <button type="submit" form="mainReportForm" formaction="{{ route('weekly.plan', $report->id) }}" class="btn-save-plan" style="background: var(--hover-bg); color: var(--text-main); border: 1px solid var(--border-color); box-shadow: none;">
                    <span>Simpan Weekly Plan</span>
                </button>
                <button type="submit" form="mainReportForm" formaction="{{ route('weekly.submit_plan', $report->id) }}" class="btn-save-plan">
                    <span>Kirim Weekly Plan</span>
                </button>
            @else
                <div style="font-size: 11px; padding: 6px 14px; border-radius: 8px; background: #dcfce7; border: 1px solid #86efac; color: #166534; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
                    <i data-feather="check" style="width: 12px; height: 12px;"></i>
                    Plan Dikirim
                </div>
            @endif
        </div>
    </div>

    <!-- Main Form -->
    <form id="mainReportForm" action="{{ route('weekly.final', $report->id) }}" method="POST">
        @csrf
        
        <!-- Top Row Grid: Objectives & Monthly Deadlines -->
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; margin-bottom: 24px;" class="top-responsive">
            
            <!-- Weekly Objective Card -->
            <div class="section-box" style="margin-bottom: 0;">
                <div class="section-header">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i data-feather="target" style="width: 16px; height: 16px; color: #2563eb;"></i>
                        Weekly Objective
                    </span>
                </div>
                <div class="section-body objectives-responsive" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px;">
                    @for($i = 0; $i < 10; $i++)
                        @php $item = $report->items->where('type', 'objective')->values()->get($i); @endphp
                        <div class="objective-row">
                            @if($item && $item->content)
                                <div class="status-checkbox" style="display: flex; align-items: center; flex-shrink: 0;">
                                    <input type="hidden" name="item_status[{{ $item->id }}]" value="{{ $item->is_completed ? '1' : '0' }}" id="status-{{ $item->id }}">
                                    <button type="button" class="checkbox-btn {{ $item->is_completed ? 'checked' : 'crossed' }}" 
                                            onclick="toggleStatus({{ $item->id }})" 
                                            style="width: 18px; height: 18px; border: 2px solid {{ $item->is_completed ? '#10b981' : '#ef4444' }}; border-radius: 5px; background: {{ $item->is_completed ? '#10b981' : '#ef4444' }}; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; transition: all 0.2s;">
                                        <i data-feather="{{ $item->is_completed ? 'check' : 'x' }}" style="width: 12px; height: 12px; color: white; stroke-width: 3;"></i>
                                    </button>
                                </div>
                            @else
                                <span style="font-size: 16px; color: var(--text-muted); flex-shrink: 0; line-height: 1; margin-left: 4px; margin-right: 4px;">•</span>
                            @endif
                            <input type="text" name="objectives[]" value="{{ $item->content ?? '' }}" 
                                class="input-line" placeholder="..." 
                                {{ $report->plan_submitted_at ? 'readonly' : '' }}>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Monthly Deadline Card -->
            <div class="section-box" style="margin-bottom: 0;">
                <div class="section-header">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i data-feather="calendar" style="width: 16px; height: 16px; color: #2563eb;"></i>
                        Deadline Bulan {{ \Carbon\Carbon::parse($report->week_start_date)->locale('id')->translatedFormat('F') }}
                    </span>
                </div>
                <div class="section-body" style="display: flex; flex-direction: column; gap: 12px;">
                    @for($i = 0; $i < 5; $i++)
                        @php $item = $report->items->where('type', 'deadline')->values()->get($i); @endphp
                        <div class="deadline-row">
                            @if($item && $item->content)
                                <div class="status-checkbox" style="display: flex; align-items: center; flex-shrink: 0;">
                                    <input type="hidden" name="item_status[{{ $item->id }}]" value="{{ $item->is_completed ? '1' : '0' }}" id="status-{{ $item->id }}">
                                    <button type="button" class="checkbox-btn {{ $item->is_completed ? 'checked' : 'crossed' }}" 
                                            onclick="toggleStatus({{ $item->id }})" 
                                            style="width: 18px; height: 18px; border: 2px solid {{ $item->is_completed ? '#10b981' : '#ef4444' }}; border-radius: 5px; background: {{ $item->is_completed ? '#10b981' : '#ef4444' }}; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; transition: all 0.2s;">
                                        <i data-feather="{{ $item->is_completed ? 'check' : 'x' }}" style="width: 12px; height: 12px; color: white; stroke-width: 3;"></i>
                                    </button>
                                </div>
                            @else
                                <span style="font-size: 16px; color: var(--text-muted); flex-shrink: 0; line-height: 1; margin-left: 4px; margin-right: 4px;">•</span>
                            @endif
                            <input type="text" name="deadlines[]" value="{{ $item->content ?? '' }}" 
                                class="input-line" placeholder="..." 
                                {{ $report->plan_submitted_at ? 'readonly' : '' }}>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Middle Row: Daily Schedule Columns Grid -->
        <div class="days-grid" style="margin-bottom: 24px;">
            @foreach($report->dailyLogs as $log)
                @php 
                    $logDate = \Carbon\Carbon::parse($log->log_date); 
                    $savedTasks = $log->description ? explode("\n", $log->description) : [];
                    $tasks = array_pad($savedTasks, 5, '');
                @endphp
                <div class="day-col">
                    <div class="day-header">
                        <div class="day-date">{{ $logDate->format('d') }}</div>
                        <div class="day-meta">
                            <div class="day-name">{{ $logDate->locale('id')->translatedFormat('l') }}</div>
                            <div class="day-month-year">{{ $logDate->locale('id')->translatedFormat('M Y') }}</div>
                        </div>
                    </div>
                    <div class="day-body">
                        <div class="task-container" id="task-container-{{ $log->id }}">
                            @foreach($tasks as $task)
                                <div class="daily-task-row">
                                    <div style="width: 16px; height: 16px; border: 1.5px solid var(--border-color); border-radius: 4px; background: transparent; flex-shrink: 0;"></div>
                                    <input type="text" name="logs[{{ $log->id }}][]" class="input-line" 
                                           value="{{ $task }}" placeholder="..." 
                                           oninput="triggerAutosave({{ $log->id }})"
                                           {{ $report->status === 'submitted' ? 'readonly' : '' }}>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($report->status !== 'submitted')
                            <button type="button" class="btn-add-task" onclick="addTaskRow({{ $log->id }})">
                                <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                                <span>Tambah</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bottom Card: Notes / Obstacles Section -->
        <div class="section-box" style="margin-bottom: 24px; background: var(--sidebar-bg); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; position: relative;">
            <div class="section-header">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i data-feather="message-square" style="width: 16px; height: 16px; color: #2563eb;"></i>
                    Catatan / Kendala (Opsional)
                </span>
            </div>
            <div style="position: relative; padding: 16px;">
                <textarea id="notesTextarea" name="notes" placeholder="Tuliskan catatan, kendala, atau hal lain yang perlu diperhatikan..." maxlength="500" style="width: 100%; height: 100px; border: none; background: transparent; color: var(--text-main); font-size: 13px; outline: none; resize: none; padding-bottom: 24px;" {{ $report->status === 'submitted' ? 'readonly' : '' }}>{{ $report->notes }}</textarea>
                <div id="charCounter" style="position: absolute; bottom: 12px; right: 16px; font-size: 11px; color: var(--text-muted); font-weight: 500;">0/500</div>
            </div>
        </div>

        <!-- Submit Button Row -->
        <div class="weekly-footer-actions" style="display: flex; justify-content: flex-end; align-items: center; margin-top: 24px; margin-bottom: 40px;">
            @if($report->status !== 'submitted')
                <button type="submit" class="btn-submit-final">
                    <!-- <i data-feather="send" style="width: 16px; height: 16px;"></i> -->
                    <span>Submit Weekly Report</span>
                </button>
            @else
                <div style="color: #10b981; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="check-circle" style="width: 16px; height: 16px;"></i>
                    <span>Laporan Selesai ({{ \Carbon\Carbon::parse($report->final_submitted_at)->format('d/m/Y H:i') }})</span>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    function toggleStatus(itemId) {
        const input = document.getElementById('status-' + itemId);
        if (!input) return;
        
        const isChecked = input.value === '1';
        const newValue = isChecked ? '0' : '1';
        input.value = newValue;
        
        const btn = input.nextElementSibling;
        
        if (newValue === '1') {
            btn.classList.remove('crossed');
            btn.classList.add('checked');
            btn.style.borderColor = '#10b981';
            btn.style.backgroundColor = '#10b981';
            btn.innerHTML = '<i data-feather="check" style="width: 12px; height: 12px; color: white; stroke-width: 3;"></i>';
        } else {
            btn.classList.remove('checked');
            btn.classList.add('crossed');
            btn.style.borderColor = '#ef4444';
            btn.style.backgroundColor = '#ef4444';
            btn.innerHTML = '<i data-feather="x" style="width: 12px; height: 12px; color: white; stroke-width: 3;"></i>';
        }
        feather.replace();
    }

    function addTaskRow(logId) {
        const container = document.getElementById('task-container-' + logId);
        
        const row = document.createElement('div');
        row.className = 'daily-task-row';
        row.style.display = 'flex';
        row.style.alignItems = 'center';
        row.style.gap = '10px';
        row.style.marginBottom = '12px';
        row.style.width = '100%';
        
        const checkbox = document.createElement('div');
        checkbox.style.width = '16px';
        checkbox.style.height = '16px';
        checkbox.style.border = '1.5px solid var(--border-color)';
        checkbox.style.borderRadius = '4px';
        checkbox.style.background = 'transparent';
        checkbox.style.flexShrink = '0';
        
        const input = document.createElement('input');
        input.type = 'text';
        input.name = `logs[${logId}][]`;
        input.className = 'input-line';
        input.placeholder = '...';
        input.style.flex = '1';
        input.style.border = 'none';
        input.style.borderBottom = '1px dashed var(--border-color)';
        input.style.background = 'transparent';
        input.style.color = 'var(--text-main)';
        input.style.padding = '4px 0';
        input.style.fontSize = '13px';
        input.style.outline = 'none';
        
        input.oninput = function() { triggerAutosave(logId); };
        
        row.appendChild(checkbox);
        row.appendChild(input);
        container.appendChild(row);
        input.focus();
    }

    let autosaveTimer;
    
    function triggerAutosave(logId) {
        clearTimeout(autosaveTimer);
        
        const container = document.getElementById('task-container-' + logId);
        const inputs = container.querySelectorAll('input');
        const tasks = Array.from(inputs).map(input => input.value);

        // Autosave logic
        autosaveTimer = setTimeout(() => {
            fetch('{{ route('weekly.autosave') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    log_id: logId, 
                    tasks: tasks 
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    console.log('Draft Autosaved successfully');
                }
            })
            .catch(error => {
                console.error('Autosave error:', error);
            });
        }, 1000); 
    }

    // Character Counter initialization
    document.addEventListener('DOMContentLoaded', () => {
        const notesTextarea = document.getElementById('notesTextarea');
        const charCounter = document.getElementById('charCounter');
        
        if (notesTextarea && charCounter) {
            const updateCounter = () => {
                const len = notesTextarea.value.length;
                charCounter.textContent = `${len}/500`;
            };
            notesTextarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
</script>
@endsection