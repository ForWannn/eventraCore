<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Absensi Bulan {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 4px 0;
            color: #000;
        }
        .meta-info {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .meta-label {
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #e8b61c;
            color: #ffffff;
            font-weight: bold;
            padding: 8px 10px;
            font-size: 11px;
            border: 1px solid #e8b61c;
            text-align: left;
        }
        td {
            padding: 6px 10px;
            border: 1px solid #f3d882;
            font-size: 11px;
            color: #333;
        }
        
        /* Alternate row styling */
        tr:nth-child(even) {
            background-color: #fffdf4;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Status highlights */
        tr.tepat-waktu-row {
            background-color: #8ae05e !important;
        }
        tr.tepat-waktu-row td {
            font-weight: bold;
            color: #000000 !important;
        }
        
        tr.lupa-absen-row {
            background-color: #f0e813 !important;
        }
        tr.lupa-absen-row td {
            font-weight: bold;
            color: #000000 !important;
        }

        tr.mangkir-row {
            background-color: #fee2e2 !important;
        }
        tr.mangkir-row td {
            font-weight: bold;
            color: #991b1b !important;
        }
    </style>
</head>
<body>
    <h2>Data Absensi Bulan {{ $monthName }} {{ $year }}</h2>
    <div class="meta-info">
        <span class="meta-label">Jam Masuk</span>
        <span>{{ $threshold }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 15%;">Jam masuk</th>
                <th style="width: 20%;">Terlambat</th>
                <th style="width: 20%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recapData as $row)
                <tr class="{{ $row['row_class'] }}">
                    <td>{{ $row['tanggal'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['jam_masuk'] }}</td>
                    <td>{{ $row['terlambat'] }}</td>
                    <td>{{ $row['catatan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
