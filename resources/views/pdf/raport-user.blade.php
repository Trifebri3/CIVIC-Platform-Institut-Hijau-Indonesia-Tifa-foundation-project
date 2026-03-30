<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>E-RAPORT CIVIC PLATFORM</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0; font-size: 10px; font-weight: bold; color: #666; }

        .user-info { margin-bottom: 25px; width: 100%; }
        .user-info td { border: none; padding: 2px 0; }

        h3 { text-transform: uppercase; font-size: 12px; border-left: 4px solid #800000; padding-left: 10px; margin-top: 20px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        th { background-color: #f5f5f5; text-transform: uppercase; font-size: 9px; font-weight: bold; padding: 10px; border: 1px solid #ddd; color: #555; }
        td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; word-wrap: break-word; }

        .text-center { text-align: center; }
        .score { font-weight: bold; color: #800000; font-size: 12px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #aaa; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>E-RAPORT DIGITAL</h2>
        <p>CIVIC EDUCATION</p>
    </div>

    <table class="user-info">
        <tr>
            <td width="15%"><strong>NAMA</strong></td>
            <td width="2%">:</td>
            <td>{{ strtoupper($user->name) }}</td>
            <td width="15%"><strong>TANGGAL CETAK</strong></td>
            <td width="2%">:</td>
            <td>{{ $date }}</td>
        </tr>
        <tr>
            <td><strong>IDENTITAS</strong></td>
            <td>:</td>
            <td>{{ $user->nim ?? 'NO-IDENTIFICATION' }}</td>
            <td><strong>WAKTU</strong></td>
            <td>:</td>
            <td>{{ $timestamp }} WIB</td>
        </tr>
    </table>

    @foreach($programs as $program)
    <h3>PROGRAM: {{ $program->title }}</h3>
    <table>
        <thead>
            <tr>
                <th width="40%">Sub-Program</th>
                <th class="text-center">Progres Materi</th>
                <th class="text-center">Presensi</th>
                <th class="text-center">Nilai Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($program->subPrograms as $sub)
    @php
        // 1. Ambil ID konten materi untuk sub-program ini
        $contentIds = $sub->contents->pluck('id');

        // 2. Hitung materi selesai (Collection whereIn butuh array murni)
        $materiSelesai = $user->progress->whereIn('sub_program_content_id', $contentIds->toArray())->count();

        // 3. Hitung presensi
        $absenCount = $sub->absensis->sum(function($absen) use ($user) {
            return $absen->kehadirans->where('user_id', $user->id)->count();
        });

        // 4. FIX NILAI: Ambil ID ujian yang ada di sub-program ini (bukan pakai Closure)
        // Kita cari modul_ujian_id yang sub_program_id-nya sama dengan $sub->id
        // Asumsi: ModulUjian punya kolom sub_program_id
        $avgScore = $user->jawaban_ujians
            ->filter(function($jawaban) use ($sub) {
                return $jawaban->ujian && $jawaban->ujian->sub_program_id == $sub->id;
            })
            ->avg('nilai') ?? 0;
    @endphp

    <tr>
        <td><strong>{{ $sub->title }}</strong></td>
        <td class="text-center">{{ $materiSelesai }} / {{ $contentIds->count() }}</td>
        <td class="text-center">{{ $absenCount }} / {{ $sub->absensis->count() }}</td>
        <td class="text-center score">{{ round($avgScore) }}</td>
    </tr>
@endforeach

        </tbody>
    </table>
    @endforeach

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem Civic Platform pada {{ $date }} {{ $timestamp }}.
    </div>
</body>
</html>
