<!DOCTYPE html>
<html>
<head>
    <title>Hasil Aktivasi - {{ $user->name }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Georgia', serif; line-height: 1.6; color: #222; }
        .title { text-align: center; text-transform: uppercase; border-bottom: 3px double #333; padding-bottom: 10px; }
        .meta { margin-bottom: 40px; }
        .question-box { margin-bottom: 25px; page-break-inside: avoid; }
        .question-title { font-weight: bold; font-size: 14px; color: #008080; margin-bottom: 5px; border-left: 5px solid #008080; padding-left: 10px; }
        .answer-text { background: #f9f9f9; padding: 15px; border: 1px solid #eee; border-radius: 5px; font-style: italic; }
        .label { font-weight: bold; width: 120px; display: inline-block; }
    </style>
</head>
<body>
    <div class="title">
        <h1>HASIL AKTIVASI PENGGUNA</h1>
    </div>

    <div class="meta">
        <p><span class="label">Nama Lengkap</span>: {{ $user->name }}</p>
        <p><span class="label">Email</span>: {{ $user->email }}</p>
        <p><span class="label">Tgl Aktivasi</span>: {{ $user->updated_at->format('d F Y') }}</p>
    </div>

    @foreach($answers as $ans)
    <div class="question-box">
        <div class="question-title">{{ $ans->question->title }}</div>
        <div class="answer-text">
            @if(is_array($ans->content))
                {{-- Menangani jika jawaban berupa checkbox/multiple --}}
                @foreach($ans->content as $key => $val)
                    @if(is_array($val))
                        <strong>{{ ucfirst($key) }}:</strong> {{ implode(', ', $val) }} <br>
                    @else
                        <strong>{{ ucfirst($key) }}:</strong> {{ $val }} <br>
                    @endif
                @endforeach
            @else
                {{ $ans->content }}
            @endif
        </div>
    </div>
    @endforeach

    <div style="margin-top: 50px; text-align: center;">
        <p>Mengetahui,</p>
        <br><br><br>
        <strong>( Direktur Eksekutif Yota )</strong>
    </div>
</body>
</html>
