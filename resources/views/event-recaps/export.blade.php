<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Event - {{ $event->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 10pt;
            vertical-align: middle;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
        }
        .kop-subtitle {
            font-size: 9pt;
            color: #475569;
            text-align: center;
        }
        .report-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <table>
        <!-- Kop Surat Resmi Perusahaan ── -->
        <tr>
            <td colspan="6" class="kop-title" style="border: none; padding-top: 10px;">EVENTRA CORE</td>
        </tr>
        <tr>
            <td colspan="6" class="kop-subtitle" style="border: none; border-bottom: 2px solid #000000; padding-bottom: 12px;">
                Jl. Raya Kenangan No. 7, Jakarta Selatan | Telp: (021) 1234567 | Email: finance@eventracore.com
            </td>
        </tr>
        
        <!-- Spacing row -->
        <tr>
            <td colspan="6" style="border: none; height: 20px;"></td>
        </tr>

        <!-- Judul Laporan -->
        <tr>
            <td colspan="6" class="report-title" style="border: none; font-weight: bold;">
                LAPORAN PERTANGGUNGJAWABAN KEUANGAN EVENT
            </td>
        </tr>
        
        <!-- Detail Event Block ── -->
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">Nama Event</td>
            <td colspan="2" style="border: none;">: {{ $event->name }}</td>
            <td class="font-bold" style="border: none;">Anggaran Awal</td>
            <td style="border: none;">: Rp {{ number_format($recap->initial_nominal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">Penyelenggara / Divisi</td>
            <td colspan="2" style="border: none;">: {{ $event->category ?? '-' }}</td>
            <td class="font-bold" style="border: none;">Total Pengeluaran</td>
            <td style="border: none;">: Rp {{ number_format($recap->total_spent, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">PIC Pelaksana</td>
            <td colspan="2" style="border: none;">: {{ $picDetails ? $picDetails->name : '-' }}</td>
            <td class="font-bold" style="border: none;">Sisa Anggaran</td>
            <td style="border: none; font-weight: bold; color: {{ $recap->remaining_budget < 0 ? '#b91c1c' : '#047857' }};">
                : {{ $recap->remaining_budget < 0 ? '-' : '' }}Rp {{ number_format(abs($recap->remaining_budget), 2, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">Tanggal Event</td>
            <td colspan="2" style="border: none;">
                : @php
                    $dates = $event->event_dates ?? [];
                    sort($dates);
                @endphp
                @if(!empty($dates))
                    {{ \Carbon\Carbon::parse($dates[0])->translatedFormat('d M Y') }}
                    @if(count($dates) > 1) - {{ \Carbon\Carbon::parse(end($dates))->translatedFormat('d M Y') }}@endif
                @else
                    -
                @endif
            </td>
            <td class="font-bold" style="border: none;">Skor Penyelesaian</td>
            <td style="border: none;">: {{ $recap->completion_score }}%</td>
        </tr>
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">Lokasi Event</td>
            <td colspan="2" style="border: none;">: {{ $event->location ?? '-' }}</td>
            <td class="font-bold" style="border: none;">Status Rekap</td>
            <td style="border: none; text-transform: uppercase;">: {{ $recap->status }}</td>
        </tr>

        <!-- Spacing row -->
        <tr>
            <td colspan="6" style="border: none; height: 15px;"></td>
        </tr>

        <!-- Table Header -->
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 25%;">Vendor</th>
                <th style="width: 25%;">Keterangan Pembelian</th>
                <th style="width: 15%; text-align: right;">Nominal</th>
            </tr>
        </thead>
        
        <!-- Table Body -->
        <tbody>
            @forelse($items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $item->date->translatedFormat('d/m/Y') }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->vendor }}</td>
                <td>{{ $item->description ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->nominal, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #64748b; font-style: italic;">
                    Belum ada bukti nota pengeluaran belanja yang dimasukkan.
                </td>
            </tr>
            @endforelse
            
            <!-- Summary Row -->
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL PENGELUARAN BELANJA:</td>
                <td class="text-right" style="color: #2563EB;">Rp {{ number_format($recap->total_spent, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
