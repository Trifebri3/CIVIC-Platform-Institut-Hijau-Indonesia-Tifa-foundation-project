<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; text-transform: uppercase; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; border-bottom: 2px solid #800000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-weight: 900; font-size: 16px; color: #800000; }
        .section { margin-bottom: 20px; }
        .label-header { font-weight: bold; background: #800000; color: #fff; font-size: 10px; padding: 5px 10px; margin-bottom: 10px; display: inline-block; border-radius: 4px; }
        .field-label { font-weight: bold; color: #666; font-size: 9px; margin-top: 10px; }
        .value { background: #fdfdfd; padding: 12px; border: 1px solid #eee; margin-bottom: 10px; border-left: 3px solid #800000; }
        table td { vertical-align: top; padding: 4px 0; }
        .text-maroon { color: #800000; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN PERTANGGUNGJAWABAN KEGIATAN</div>
        <div style="font-size: 10px; margin-top: 5px; font-weight: bold;">PERIODE: {{ $report->period->name }}</div>
    </div>

    <div class="section">
        <div class="label-header">IDENTITAS PROGRAM & PESERTA</div>
        <table width="100%">
            {{-- AMBIL PROGRAM DARI RELASI USER --}}
            @php
                $program = $report->user->programs->first();
            @endphp
            <tr>
                <td width="25%" class="field-label">NAMA PROGRAM</td>
                <td class="text-maroon">: {{ $program->name ?? 'PROGRAM UMUM / TIDAK TERIKAT' }}</td>
            </tr>
            <tr>
                <td class="field-label">NAMA MAHASISWA</td>
                <td>: {{ $report->user->name }}</td>
            </tr>
            <tr>
                <td class="field-label">EMAIL / ID</td>
                <td>: {{ $report->user->email }}</td>
            </tr>
            <tr>
                <td class="field-label">STATUS LAPORAN</td>
                <td>: {{ strtoupper($report->status) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="label-header">ISI LAPORAN (HASIL KEGIATAN)</div>
        @foreach($report->template->fields as $field)
            <div class="field-label">{{ $field['label'] }}</div>
            <div class="value">
                @php $val = $report->content[$field['name']] ?? '-'; @endphp

                @if($field['type'] == 'image' || $field['type'] == 'file')
                    {{-- Jika ini file, tampilkan path-nya (karena PDF tidak bisa render file sembarangan) --}}
                    <span style="color: #666; font-style: italic; font-size: 8px;">
                        [Lampiran Berkas: {{ basename($val) }}]
                    </span>
                @else
                    {{-- Gunakan nl2br jika inputnya textarea supaya enter-nya kelihatan --}}
                    {!! nl2br(e($val)) !!}
                @endif
            </div>
        @endforeach
    </div>

    <div style="margin-top: 50px; text-align: right; font-size: 9px; color: #aaa;">
        Dicetak otomatis oleh sistem pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
