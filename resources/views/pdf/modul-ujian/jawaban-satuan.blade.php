<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 3px solid #800000; padding-bottom: 10px; margin-bottom: 20px; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px; font-size: 12px; }
        .question-box { margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-left: 5px solid #800000; }
        .question-text { font-weight: bold; font-size: 13px; color: #800000; margin-bottom: 10px; }
        .answer-text { font-size: 12px; background: white; padding: 10px; border: 1px solid #eee; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 10px; text-align: center; color: #aaa; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0; color: #800000;">{{ $title }}</h2>
        <p style="margin:5px 0; font-size: 14px; font-weight: bold;">{{ $modul->judul }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="150">Nama Mahasiswa</td>
            <td>: {{ $user->name }}</td>
            <td width="100">Nilai Akhir</td>
            <td style="font-size: 20px; font-weight: bold; color: #800000;">: {{ $jawaban->nilai ?? '0' }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>: {{ $user->email }}</td>
            <td>Status</td>
            <td>: {{ $jawaban->nilai ? 'Lulus/Dinilai' : 'Belum Dinilai' }}</td>
        </tr>
    </table>

    <div class="content">
        @foreach($modul->konfigurasi_soal as $soal)
            <div class="question-box">
                <div class="question-text">Q: {{ $soal['pertanyaan'] }}</div>
                <div class="answer-text">
                    @php $userAns = $jawaban->konten_jawaban[$soal['id']] ?? '-'; @endphp
                    @if(str_contains($userAns, 'jawaban-ujian/'))
                        [File Attachment: {{ $userAns }}]
                    @else
                        {{ $userAns }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($jawaban->feedback_admin)
    <div style="margin-top: 30px; padding: 15px; border: 1px dashed #800000;">
        <strong style="font-size: 12px;">Catatan/Feedback Admin:</strong>
        <p style="font-size: 12px; font-style: italic;">"{{ $jawaban->feedback_admin }}"</p>
    </div>
    @endif

    <div class="footer">
        Dicetak otomatis oleh System Civic pada {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
