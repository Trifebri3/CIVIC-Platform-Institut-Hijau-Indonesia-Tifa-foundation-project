<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #800000; color: white; text-transform: uppercase; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">REKAPITULASI PENGELUARAN SURAT</h2>
        <p style="margin:5px 0;">INSTITUT HIJAU INDONESIA (IHI)</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tgl Pengajuan</th>
                <th>Nomor Surat</th>
                <th>Nama Pengaju</th>
                <th>Wilayah FGD</th>
                <th>Penerima</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($surat as $item)
            <tr>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->nomor_surat ?? 'Belum Terbit' }}</td>
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->wilayah_kegiatan }}</td>
                <td>{{ $item->penerima_surat }}</td>
                <td>{{ strtoupper($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
