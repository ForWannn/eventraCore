<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Izin Cuti Tahunan</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #000000;
            line-height: 1.6;
            padding: 60px 60px 180px 60px;
            margin: 0px;
            box-sizing: border-box;
            height: 100%;
        }
        .date-section {
            margin-bottom: 25px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .meta-table td {
            padding: 0;
            vertical-align: top;
        }
        .recipient-section {
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .salutation {
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            width: 100px;
            font-weight: bold;
        }
        .info-colon {
            width: 20px;
            font-weight: bold;
        }
        .info-value {
            font-weight: bold;
        }
        .body-text {
            text-align: justify;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .closing-text {
            margin-bottom: 40px;
            line-height: 1.5;
        }
        .signatures-container {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures-container td {
            vertical-align: top;
            text-align: center;
        }
        .signature-title {
            font-size: 14px;
            margin-bottom: 75px;
        }
        .signature-name {
            font-size: 14px;
        }
        .signature-role {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }
        .footer-graphic {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: -10;
        }
        .footer-graphic img {
            width: 100%;
            display: block;
        }
    </style>
</head>
<body>

    @php
        // Ensure Carbon uses Indonesian locale inside the template
        \Carbon\Carbon::setLocale('id');
        
        $duration = 0;
        $temp = $leaveRequest->start_date->copy();
        while ($temp->lessThanOrEqualTo($leaveRequest->end_date)) {
            if (!$temp->isSaturday() && !$temp->isSunday()) {
                $duration++;
            }
            $temp->addDay();
        }
        $start_day = $leaveRequest->start_date->translatedFormat('l');
        $start_date_formatted = $leaveRequest->start_date->translatedFormat('d F Y');
        $end_day = $leaveRequest->end_date->translatedFormat('l');
        $end_date_formatted = $leaveRequest->end_date->translatedFormat('d F Y');
        
        $ceoName = $ceo ? $ceo->name : 'Bobby Hendra Saputra';
        $gmName = $gm ? $gm->name : 'M. Agus Idham';
        $userDivision = $leaveRequest->user->division->name ?? 'Staff';
    @endphp

    <div class="date-section">
        Palembang, {{ $leaveRequest->created_at->translatedFormat('d F Y') }}
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 80px;">Hal</td>
            <td style="width: 20px;">:</td>
            <td style="font-weight: bold;">Surat Izin Cuti Tahunan</td>
        </tr>
    </table>

    <div class="recipient-section">
        Yth. General Manager dan Director<br>
        CV.Beststar Sumatera (Reel Seven Organizer)<br>
        Ditempat
    </div>

    <div class="salutation">
        Dengan hormat,<br>
        Saya yang bertanda tangan di bawah ini:
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $leaveRequest->user->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Divisi</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $userDivision }}</td>
        </tr>
    </table>

    <div class="body-text">
        Bermaksud mengajukan cuti tahunan selama {{ $duration }} hari, yaitu pada hari {{ $start_day }}, {{ $start_date_formatted }} sampai dengan {{ $end_day }}, {{ $end_date_formatted }} Dengan maksud {{ trim($leaveRequest->reason, '.') }}.
    </div>

    <div class="closing-text">
        Demikian permohonan cuti ini saya ajukan.<br>
        Terima kasih atas perhatian Bapak/Ibu sekalian.
    </div>

    <!-- Signatures Table -->
    <table class="signatures-container" style="margin-top: 30px;">
        <tr>
            <!-- Left Signature: GM -->
            <td style="width: 50%;">
                <div class="signature-title">Mengetahui,</div>
                <div class="signature-name" style="font-weight: bold; text-decoration: underline;">{{ $gmName }}</div>
                <div class="signature-role">General Manager</div>
            </td>
            <!-- Right Signature: Director -->
            <td style="width: 50%;">
                <div class="signature-title">Menyetujui,</div>
                <div class="signature-name" style="font-weight: bold; text-decoration: underline;">{{ $ceoName }}</div>
                <div class="signature-role">Director</div>
            </td>
        </tr>
        <tr>
            <!-- Bottom Signature: Applicant (Pemohon) centered in the middle of page -->
            <td colspan="2" style="padding-top: 50px; text-align: center;">
                <div class="signature-title" style="margin-bottom: 75px;">Pemohon,</div>
                <div class="signature-name">{{ $leaveRequest->user->name }}</div>
                <div class="signature-role">{{ $userDivision }}</div>
            </td>
        </tr>
    </table>
    <!-- Footer Graphic Image -->
    <div class="footer-graphic">
        <img src="{{ public_path('assets/Images/footer.png') }}">
    </div>
</body>
</html>
