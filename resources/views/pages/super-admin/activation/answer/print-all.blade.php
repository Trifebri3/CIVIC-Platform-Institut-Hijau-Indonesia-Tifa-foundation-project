<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #800000;
            padding-bottom: 15px;
        }
        .header h1 {
            text-transform: uppercase;
            font-size: 14pt;
            margin: 0;
            color: #800000;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 8pt;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th {
            background-color: #800000;
            color: #ffffff;
            padding: 12px 8px;
            text-align: left;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #800000;
        }
        td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            word-wrap: break-word;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .user-name {
            font-weight: bold;
            color: #111827;
            font-size: 10pt;
        }
        .user-email {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 2px;
        }
        .answer-item {
            margin-bottom: 12px;
            padding-left: 10px;
            border-left: 2px solid #f3f4f6;
        }
        .q-title {
            font-weight: bold;
            display: block;
            color: #800000;
            font-size: 8pt;
            margin-bottom: 3px;
        }
        .q-content {
            color: #4b5563;
            font-size: 8.5pt;
        }
        .field-value {
            margin-bottom: 2px;
            display: block;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekapitulasi Aktivasi Peserta</h1>
        <p>CIVIC Platform • Laporan Digital • {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="width: 140px;">Data Peserta</th>
                <th>Detail Jawaban Aktivasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td style="text-align: center; font-size: 8pt; color: #999;">{{ $index + 1 }}</td>
                <td>
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                    <div style="font-size: 7pt; margin-top: 8px; color: #9ca3af; font-style: italic;">
                        Selesai pada:<br>{{ $user->updated_at->format('d M Y, H:i') }} WIB
                    </div>
                </td>
                <td>
                    @if($user->activationAnswers && $user->activationAnswers->count() > 0)
                        @foreach($user->activationAnswers as $ans)
                        <div class="answer-item">
                            <span class="q-title">{{ $ans->question->title ?? 'Pertanyaan' }}</span>
                            <div class="q-content">
                                @if(is_array($ans->content))
                                    {{-- Membersihkan key aneh dan hanya mengambil value --}}
                                    @foreach($ans->content as $value)
                                        <div class="field-value">• {{ is_array($value) ? implode(', ', $value) : ($value ?: '-') }}</div>
                                    @endforeach
                                @else
                                    {{ $ans->content ?: '-' }}
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <span style="color: #9ca3af; font-style: italic; font-size: 8pt;">Belum mengisi jawaban aktivasi.</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; padding: 40px; color: #9ca3af;">Belum ada data peserta yang terekam dalam sistem.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem CIVIC | {{ date('d/m/Y H:i:s') }} | Dokumen Rahasia Institusi
    </div>
</body>
</html>
