<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Event - {{ $event->name }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; font-size: 10pt; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .kop-title { font-size: 16pt; font-weight: bold; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-align: center; text-transform: uppercase; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="kop-title">REKAP KEUANGAN & PENGELUARAN OPS</div>
    <div class="report-title">{{ $event->name }}</div>

    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">NAMA EVENT</td>
            <td colspan="8" style="border: none;">: {{ $event->name }}</td>
        </tr>
        <tr>
            <td colspan="2" class="font-bold" style="border: none;">TANGGAL EVENT</td>
            <td colspan="8" style="border: none;">: {{ \Carbon\Carbon::parse($event->start_date)->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="background-color: #FCE4D6;">NO</th>
                <th class="text-center" style="background-color: #FCE4D6;">TANGGAL</th>
                <th class="text-center" style="background-color: #FCE4D6;">NAMA ITEM</th>
                <th class="text-center" style="background-color: #FCE4D6;">VENDOR</th>
                <th class="text-center" style="background-color: #FCE4D6;">KATEGORI</th>
                <th class="text-center" style="background-color: #FCE4D6;">QTY</th>
                <th class="text-center" style="background-color: #FCE4D6;">HARGA SATUAN</th>
                <th class="text-center" style="background-color: #D9EAD3;">DEBET</th>
                <th class="text-center" style="background-color: #F4CCCC;">KREDIT</th>
                <th class="text-center" style="background-color: #FFF2CC;">SALDO</th>
                <th class="text-center" style="background-color: #FCE4D6;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @php 
                // Calculate total automatic adjustments to deduct/add to the latest budget
                // to reconstruct the original "Saldo Awal"
                $netAdjustments = 0;
                foreach($items as $item) {
                    if ($item->item_name === 'Penyesuaian Anggaran (Otomatis)') {
                        $isPemasukan = in_array(strtolower(trim($item->category)), ['tambahan ops', 'penambahan saldo', 'pemasukan']);
                        if ($isPemasukan) {
                            $netAdjustments += $item->nominal;
                        } else {
                            $netAdjustments -= $item->nominal;
                        }
                    }
                }

                $originalInitialNominal = ($recap->initial_nominal ?? 0) - $netAdjustments;
                $saldoBerjalan = $originalInitialNominal;
                $totalDebet = $saldoBerjalan; 
                $totalKredit = 0;
            @endphp
            
            {{-- BARIS SALDO AWAL --}}
            <tr>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="font-bold">Saldo Awal (Pencairan Anggaran)</td>
                <td>Sistem / Finance</td>
                <td class="text-center">Modal Awal</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($originalInitialNominal, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #274E13;">Rp {{ number_format($originalInitialNominal, 0, ',', '.') }}</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>

            {{-- TRANSAKSI --}}
            @forelse($items as $index => $item)
                @php
                    $isPemasukan = in_array(strtolower(trim($item->category)), ['tambahan ops', 'penambahan saldo', 'pemasukan']);
                    $debet = $isPemasukan ? $item->nominal : 0;
                    $kredit = !$isPemasukan ? $item->nominal : 0;
                    
                    if ($isPemasukan) {
                        $saldoBerjalan += $debet;
                        $totalDebet += $debet;
                    } else {
                        $saldoBerjalan -= $kredit;
                        $totalKredit += $kredit;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                    <td>{{ $item->item_name ?? '-' }}</td>
                    <td>{{ $item->vendor }}</td>
                    <td class="text-center">{{ $item->category }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #274E13;">{{ $debet > 0 ? 'Rp ' . number_format($debet, 0, ',', '.') : '-' }}</td>
                    <td class="text-right" style="color: #990000;">{{ $kredit > 0 ? 'Rp ' . number_format($kredit, 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Data transaksi tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right font-bold" style="background-color: #FCE4D6;">GRAND TOTAL</td>
                <td class="text-right font-bold" style="background-color: #D9EAD3;">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="background-color: #F4CCCC;">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="background-color: #FFF2CC;">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</td>
                <td style="background-color: #FCE4D6;"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>