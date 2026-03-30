<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aktivasi - {{ $user->name }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', sans-serif;
            background-color: #ffffff;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .sidebar-accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 12px;
            background: #800000; /* Konsisten dengan tema Marun CIVIC */
        }
        .container {
            padding: 50px 60px 40px 80px;
        }
        .header {
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 25px;
            margin-bottom: 35px;
        }
        .header h1 {
            color: #800000;
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .user-meta {
            margin-top: 8px;
            font-size: 9pt;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 40px;
            background: #fcfcfc;
            border: 1px solid #f1f1f1;
            padding: 25px;
            border-radius: 15px;
        }
        .info-grid td {
            padding: 6px 0;
            font-size: 10.5pt;
        }
        .label {
            color: #800000;
            font-weight: bold;
            width: 160px;
        }
        .section-title {
            font-size: 13pt;
            color: #111827;
            border-bottom: 2px solid #800000;
            display: inline-block;
            padding-bottom: 5px;
            margin: 20px 0 25px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .answer-card {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .question-text {
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 10px;
            display: block;
            font-size: 11pt;
            background: #f8f8f8;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .answer-content {
            padding: 5px 12px 15px 12px;
            font-size: 10.5pt;
            line-height: 1.7;
            color: #444;
        }
        .field-item {
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 8pt;
            color: #bbb;
            border-top: 1px solid #f5f5f5;
            padding-top: 20px;
        }
        .signature-space {
            margin-top: 80px;
        }
        .signature-box {
            float: right;
            width: 220px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar-accent"></div>

    <div class="container">
        <div class="header">
            <h1>Profil Aktivasi</h1>
            <div class="user-meta">CIVIC Platform • Institut Hijau Indonesia</div>
        </div>

        <table class="info-grid">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td>: {{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>: {{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Selesai</td>
                <td>: {{ $user->updated_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>

        <div class="section-title">Hasil Jawaban</div>

        @forelse($answers as $ans)
            <div class="answer-card">
                <span class="question-text">{{ $ans->question->title }}</span>
                <div class="answer-content">
                    @if(is_array($ans->content))
                        {{-- Logika untuk menghilangkan "Field ID" aneh --}}
                        @foreach($ans->content as $val)
                            <div class="field-item">
                                @if(is_array($val))
                                    {{ implode(', ', $val) }}
                                @else
                                    {{ $val ?: '-' }}
                                @endif
                            </div>
                        @endforeach
                    @else
                        {{ $ans->content ?: 'Tidak ada jawaban.' }}
                    @endif
                </div>
            </div>
        @empty
            <p style="font-style: italic; color: #9ca3af;">Tidak ada data jawaban.</p>
        @endforelse

        <div class="signature-space">
            <div class="signature-box">
                <p>Jakarta, {{ date('d F Y') }}</p>
                <br><br><br>
                <div style="font-weight: bold; color: #1a1a1a;">
                    Administrator CIVIC
                </div>
                <div style="font-size: 8pt; color: #999; margin-top: 5px;">
                    Verified Digital Signature
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            Dokumen ini dihasilkan secara otomatis oleh CIVIC Platform.<br>
            ID Referensi: #CIVIC-{{ $user->id }}-{{ date('YmdHi') }}
        </div>
    </div>
</body>
</html>
