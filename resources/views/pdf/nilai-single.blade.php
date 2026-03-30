<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        /* Setup Halaman A4 */
        @page { margin: 0; size: a4; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0; padding: 0;
            background-color: #fff;
            color: #1a1a1a;
            text-transform: uppercase;
        }

        /* Border Mewah Civic - Dibuat lebih tipis agar hemat ruang */
        .page-border {
            border: 10px solid #800000;
            margin: 15px;
            padding: 30px;
            min-height: 90vh; /* Menggunakan min-height agar dinamis */
            position: relative;
        }

        /* Header Section - Dibuat lebih rapat */
        .header {
            text-align: center;
            border-bottom: 2px double #800000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .brand { font-size: 24px; font-weight: 900; font-style: italic; letter-spacing: -1px; margin: 0; }
        .brand span { color: #800000; }
        .subtitle { font-size: 8px; letter-spacing: 3px; color: #666; font-weight: bold; margin-top: 3px; }

        /* User Info Section - Lebih Compact */
        .content-body { margin-top: 10px; }
        .info-group { margin-bottom: 15px; }
        .label { font-size: 8px; color: #800000; font-weight: 900; letter-spacing: 1px; }
        .value { font-size: 16px; font-weight: 900; margin: 2px 0; border-left: 3px solid #800000; padding-left: 12px; font-style: italic; }

        /* Table Styling - Diperkecil ukurannya */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th {
            background-color: #800000;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            letter-spacing: 1px;
        }
        td {
            border-bottom: 1px solid #eee;
            padding: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        .score-box {
            text-align: center;
            color: #800000;
            font-size: 14px;
            font-weight: 900;
        }

        /* Footer Section - Menggunakan Margin alih-alih Absolute Berlebihan */
        .footer-container {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            display: block;
            width: 100%;
        }

        .footer-flex {
            width: 100%;
        }

        .meta-data {
            float: left;
            width: 70%;
            font-size: 8px;
            color: #999;
            line-height: 1.5;
        }

        .qr-section {
            float: right;
            width: 25%;
            text-align: center;
        }

        .qr-box {
            display: inline-block;
            padding: 4px;
            border: 1px solid #eee;
            background: white;
        }

        .qr-caption {
            font-size: 6px;
            font-weight: 900;
            color: #800000;
            margin-top: 3px;
            letter-spacing: 1px;
        }

        /* Badge Validasi - Dikecilkan */
        .verified-badge {
            position: absolute;
            top: 110px;
            right: 30px;
            border: 1.5px solid #000;
            padding: 6px;
            transform: rotate(15deg);
            opacity: 0.1;
            font-weight: 900;
            font-size: 10px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="page-border">
        <div class="verified-badge">AUTHENTIC DOCUMENT</div>

        <div class="header">
            <h1 class="brand">CIVIC <span>PLATFORM</span></h1>
            <div class="subtitle">CERTIFICATE OF PERFORMANCE & VALIDATION</div>
        </div>

        <div class="content-body">
            <div class="info-group">
                <div class="label">HOLDER NAME:</div>
                <div class="value">{{ $data->user->name }}</div>
            </div>

            <div class="info-group">
                <div class="label">PROGRAM NAME:</div>
                <div class="value">{{ $data->template->template_name }}</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="75%">ASSESSMENT CRITERIA</th>
                        <th width="25%" style="text-align: center;">RESULT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->template->schema['kriteria'] as $crit)
                    <tr>
                        <td>{{ $crit['label'] }}</td>
                        <td class="score-box">{{ $data->isi_nilai[$crit['key']] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer-container clearfix">
            <div class="meta-data">
                <strong>DOCUMENT ID:</strong> {{ strtoupper($data->qr_code_secret) }}<br>
                <strong>DATE PUBLISHED:</strong> {{ $data->created_at->format('d F Y') }}<br>
                <strong>VALIDATION:</strong> CIVIC AUTOMATION SYSTEM V.1
            </div>

            <div class="qr-section">
                <div class="qr-box">
                    <img src="{{ $qrcode_path }}" width="70" height="70">
                </div>
                <div class="qr-caption">SCAN TO VERIFY</div>
            </div>
        </div>
    </div>
</body>
</html>
