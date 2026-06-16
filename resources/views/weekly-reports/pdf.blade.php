<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Report - {{ $user->name }} - {{ $report->week_start_date->format('Y-m-d') }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 11.5px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .header-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
            margin-bottom: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11.5px;
        }
        .meta-label {
            font-weight: bold;
            color: #4b5563;
            width: 130px;
        }
        .meta-value {
            color: #1f2937;
        }
        .badge-progress {
            display: inline-block;
            background-color: #10b981;
            color: white;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10.5px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .objectives-grid {
            width: 100%;
            margin-bottom: 16px;
        }
        .objective-item {
            padding: 5px 0;
            border-bottom: 1px dashed #f3f4f6;
            font-size: 11px;
        }
        .checkbox-container {
            display: inline-block;
            width: 18px;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #2563eb;
            vertical-align: middle;
        }
        .objective-text {
            display: inline-block;
            vertical-align: middle;
        }
        .deadlines-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .deadline-item {
            padding: 4px 0;
            font-size: 11px;
            border-bottom: 1px dashed #f3f4f6;
        }
        .deadline-item:before {
            content: "• ";
            color: #ef4444;
            font-weight: bold;
            margin-right: 6px;
        }
        .daily-log-day {
            margin-bottom: 12px;
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .daily-log-header {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }
        .daily-log-body {
            font-size: 10.5px;
            color: #4b5563;
        }
        .daily-task-item {
            padding: 2px 0;
        }
        .daily-task-item:before {
            content: "— ";
            color: #9ca3af;
            margin-right: 4px;
        }
        .notes-box {
            background-color: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 6px;
            padding: 10px;
            font-size: 10.5px;
            color: #4b5563;
            min-height: 30px;
        }
        .row {
            width: 100%;
        }
        .col-6 {
            width: 50%;
            float: left;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="header-title">Weekly Report</h1>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama</td>
            <td class="meta-value">: {{ $user->name }}</td>
            <td class="meta-label">ID</td>
            <td class="meta-value">: {{ $user->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Divisi</td>
            <td class="meta-value">: {{ optional($user->division)->name ?? '-' }}</td>
            <td class="meta-label">Periode</td>
            <td class="meta-value">: {{ $dateRangeString }}</td>
        </tr>
        <tr>
            <td class="meta-label">Progress</td>
            <td class="meta-value" colspan="3">: 
                <span class="badge-progress">{{ $report->completion_percentage }}%</span>
            </td>
        </tr>
    </table>

    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <!-- Left Column: Weekly Objectives (65% width) -->
            <td style="width: 65%; vertical-align: top; padding-right: 20px;">
                <div class="section-title">Weekly Objectives</div>
                @if($canViewPlan)
                    <table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                        <tr>
                            <!-- Sub-column 1: Items 1-5 -->
                            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                                @php $objectivesVal = $report->items->where('type', 'objective')->values(); @endphp
                                @for($i = 0; $i < 5; $i++)
                                    @php $item = $objectivesVal->get($i); @endphp
                                    @if($item)
                                        <div class="objective-item">
                                            <span class="checkbox-container">
                                                @if($item->is_completed)
                                                    &#x2611;
                                                @else
                                                    &#x2610;
                                                @endif
                                            </span>
                                            <span class="objective-text">{{ $item->content }}</span>
                                        </div>
                                    @endif
                                @endfor
                                @if($objectivesVal->count() == 0)
                                    <div style="font-style: italic; color: #9ca3af; font-size: 11px;">Tidak ada objective minggu ini</div>
                                @endif
                            </td>
                            <!-- Sub-column 2: Items 6-10 -->
                            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                                @for($i = 5; $i < 10; $i++)
                                    @php $item = $objectivesVal->get($i); @endphp
                                    @if($item)
                                        <div class="objective-item">
                                            <span class="checkbox-container">
                                                @if($item->is_completed)
                                                    &#x2611;
                                                @else
                                                    &#x2610;
                                                @endif
                                            </span>
                                            <span class="objective-text">{{ $item->content }}</span>
                                        </div>
                                    @endif
                                @endfor
                            </td>
                        </tr>
                    </table>
                @else
                    <div style="font-style: italic; color: #9ca3af; font-size: 11px; padding: 5px 0;">Weekly Plan belum dikirim.</div>
                @endif
            </td>
            <!-- Right Column: Deadline Bulan Ini (35% width) -->
            <td style="width: 35%; vertical-align: top;">
                <div class="section-title">Deadline Bulan Ini</div>
                @if($canViewPlan)
                    <ul class="deadlines-list">
                        @php $deadlines = $report->items->where('type', 'deadline'); @endphp
                        @forelse($deadlines as $item)
                            <li class="deadline-item">{{ $item->content }}</li>
                        @empty
                            <li style="font-style: italic; color: #9ca3af; font-size: 11px;">Tidak ada deadline bulan ini</li>
                        @endforelse
                    </ul>
                @else
                    <div style="font-style: italic; color: #9ca3af; font-size: 11px; padding: 5px 0;">Weekly Plan belum dikirim.</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Kegiatan Harian</div>
    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 15px;">
        <tr>
            @foreach($report->dailyLogs as $log)
                @php 
                    $logDate = \Carbon\Carbon::parse($log->log_date); 
                    $tasks = array_filter(explode("\n", $log->description ?? ''));
                @endphp
                <td style="width: 20%; @if(!$loop->last) border-right: 1px solid #e5e7eb; @endif vertical-align: top; padding: 8px; box-sizing: border-box; background-color: #f9fafb;">
                    <div style="font-size: 10.5px; font-weight: bold; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin-bottom: 5px;">
                        <div style="font-size: 13px; font-weight: bold; line-height: 1;">{{ $logDate->format('d') }}</div>
                        <div style="font-size: 8.5px; color: #6b7280; text-transform: uppercase; margin-top: 1px;">{{ $logDate->locale('id')->translatedFormat('l') }}</div>
                    </div>
                    <div style="font-size: 9.5px; color: #4b5563; line-height: 1.35;">
                        @forelse($tasks as $t)
                            <div style="padding: 2px 0; border-bottom: 1px dashed #f3f4f6;">&bull; {{ $t }}</div>
                        @empty
                            <div style="color: #9ca3af; font-style: italic; font-size: 9px;">Tidak ada log</div>
                        @endforelse
                    </div>
                </td>
            @endforeach
        </tr>
    </table>

    <div class="section-title">Catatan Karyawan (Notes)</div>
    <div class="notes-box">
        {!! nl2br(e($report->notes ?? 'Tidak ada catatan tambahan.')) !!}
    </div>
</body>
</html>
