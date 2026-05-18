@extends('layouts.app')

@section('title', 'Daftar Event')

@section('content')
<style>
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
    th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border-color); }
    th { color: var(--text-muted); font-weight: 500; font-size: 13px; }
    .btn-create { display: inline-block; padding: 10px 16px; background: var(--primary); color: var(--primary-text); text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 500; transition: opacity 0.2s; }
    .btn-create:hover { opacity: 0.9; }
    .badge { padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 500; }
    .badge-upcoming  { background: #dbeafe; color: #1e3a8a; }
    .badge-ongoing   { background: #fef08a; color: #854d0e; }
    .badge-completed { background: #dcfce7; color: #166534; }
    .pic-cell { display: flex; align-items: center; gap: 8px; }
    .avatar-xs { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); flex-shrink: 0; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }

    .empty-state { text-align: center; padding: 48px 24px; color: var(--text-muted); }
    .empty-state svg { width: 48px; height: 48px; margin-bottom: 16px; opacity: 0.3; }
    .empty-state p { font-size: 14px; }
</style>

<div class="card">
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
        <div style="flex:1;">
            <h3 style="margin-bottom: 4px;">Daftar Event</h3>
            <p style="font-size: 13px; color: var(--text-muted);">
                @role('CEO|GM')
                    Daftar event berdasarkan bulan yang diplih.
                @else
                    Event yang Anda ditugaskan pada bulan ini.
                @endrole
            </p>
        </div>
        
        <form method="GET" action="{{ route('events.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <select name="month" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; background: var(--bg-color); color: var(--text-main);">
                @foreach(range(1, 12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endforeach
            </select>
            
            <select name="year" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; background: var(--bg-color); color: var(--text-main);">
                @foreach(range(date('Y') - 2, date('Y') + 2) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            @role('CEO')
            <a href="{{ route('events.create') }}" class="btn-create" style="margin-left: 10px;">+ Buat Event Baru</a>
            @endrole
        </form>
    </div>

    @if($events->isEmpty())
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <p>
                @role('CEO|GM')
                    Belum ada event. Mulai dengan membuat event baru.
                @else
                    Anda belum ditugaskan pada event apapun.
                @endrole
            </p>
        </div>
    @else
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Jadwal</th>
                    <th>PIC Event</th>
                    <th>Posisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                @php
                    $pic = $event->participants->where('pivot.is_pic', true)->first();
                    $totalMembers = $event->positions->sum(fn($p) => $p->members_count ?? 0);
                @endphp
                <tr>
                    <td style="font-weight: 500;">
                        {{ $event->name }}
                        @if($event->description)
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ Str::limit($event->description, 50) }}</div>
                        @endif
                    </td>
                    <td style="color: var(--text-muted); font-size: 13px;">
                        @php
                            $dates = $event->event_dates ?? [];
                            $count = count($dates);
                        @endphp
                        @if($count > 0)
                            @php
                                sort($dates);
                                $displayDates = collect($dates)->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'));
                                $year = \Carbon\Carbon::parse($dates[0])->format('Y');
                            @endphp
                            {{ $displayDates->implode(', ') }} {{ $year }}
                        @else
                            -
                        @endif
                        @if($event->start_time && $event->end_time)
                            <br><span style="font-size:11px;">Jam: {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($pic)
                            <div class="pic-cell">
                                <img src="{{ $pic->photo_url }}" class="avatar-xs" alt="{{ $pic->name }}">
                                <span>{{ $pic->name }}</span>
                            </div>
                        @else
                            <span style="color:var(--text-muted);font-size:12px;">Belum ditentukan</span>
                        @endif
                    </td>
                    <td style="color: var(--text-muted); font-size: 13px;">
                        {{ $event->positions->count() }} posisi
                    </td>
                    <td>
                        <span class="badge badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('events.show', $event->id) }}"
                           style="color:var(--text-muted);text-decoration:none;font-size:13px;font-weight:500;">
                            Detail →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
