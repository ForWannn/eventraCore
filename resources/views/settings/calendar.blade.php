@extends('layouts.app')

@section('title', 'Pengaturan Kalender Kerja')

@section('content')
<style>
    .calendar-settings-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 32px;
    }
    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .settings-title-section {
        display: flex;
        flex-direction: column;
    }
    .settings-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    .settings-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    /* Month Selector Form */
    .selector-form {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .select-input {
        padding: 8px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--hover-bg);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        outline: none;
    }

    /* Calendar Day List Table */
    .calendar-table-wrapper {
        margin-top: 20px;
        overflow-x: auto;
    }
    .calendar-table {
        width: 100%;
        border-collapse: collapse;
    }
    .calendar-table th, .calendar-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    .calendar-table th {
        font-size: 12px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .calendar-table tr.weekend {
        background: rgba(239, 68, 68, 0.01);
    }
    [data-theme="dark"] .calendar-table tr.weekend {
        background: rgba(239, 68, 68, 0.02);
    }

    /* Switch styling */
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }
    [data-theme="dark"] .slider {
        background-color: #475569;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #2563eb;
    }
    input:checked + .slider:before {
        transform: translateX(20px);
    }

    /* Text Description Input */
    .desc-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--text-main);
        font-size: 13px;
        outline: none;
        transition: border-color 0.15s;
    }
    .desc-input:focus {
        border-color: #2563eb;
    }

    /* Save button section */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 28px;
    }
    .btn-save {
        padding: 10px 24px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover {
        opacity: 0.9;
    }
</style>

<div class="calendar-settings-card">
    <div class="settings-header">
        <div class="settings-title-section">
            <h1 class="settings-title">Pengaturan Kalender Kerja</h1>
            <p class="settings-subtitle">Konfigurasikan hari kerja aktif dan hari libur untuk perhitungan statistik kehadiran.</p>
        </div>

        <form action="{{ route('settings.calendar') }}" method="GET" class="selector-form" id="monthSelectorForm">
            <select name="month" class="select-input" onchange="document.getElementById('monthSelectorForm').submit()">
                @for($m = 1; $m <= 12; $m++)
                    @php $mStr = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $mStr }}" {{ $month == $mStr ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(2024, $m, 1)->locale('id')->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="select-input" onchange="document.getElementById('monthSelectorForm').submit()">
                @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.calendar.update') }}" method="POST">
        @csrf
        
        <div class="calendar-table-wrapper">
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 15%;">Hari</th>
                        <th style="width: 20%;">Hari Kerja?</th>
                        <th style="width: 55%;">Keterangan (Opsional)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dates as $date)
                        <tr class="{{ $date['is_weekend'] ? 'weekend' : '' }}">
                            <td style="font-weight: 700; color: var(--text-main);">
                                {{ $date['day_num'] }}
                            </td>
                            <td style="font-weight: 600; color: var(--text-muted);">
                                {{ $date['day_name'] }}
                            </td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" name="dates[{{ $date['date'] }}][is_working_day]" value="1" {{ $date['is_working_day'] ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td>
                                <input type="text" name="dates[{{ $date['date'] }}][description]" class="desc-input" value="{{ $date['description'] }}" placeholder="Liburan, Cuti Bersama,dll">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i data-feather="save" style="width: 16px; height: 16px;"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

@endsection
