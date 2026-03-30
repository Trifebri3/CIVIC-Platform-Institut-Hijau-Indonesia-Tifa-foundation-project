<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #800000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #800000; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #800000; color: white; padding: 10px; text-transform: uppercase; font-size: 10px; }
        td { border: 1px solid #eee; padding: 8px; text-align: left; }
        tr:nth-child(even) { background-color: #fafafa; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div style="margin-top: 5px;">Modul: {{ $modul->judul }}</div>
        <div style="font-size: 10px;">Dicetak pada: {{ $date }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Mahasiswa</th>
                <th>Email</th>
                <th width="60" style="text-align: center;">Nilai</th>
                <th>Feedback / Catatan Admin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($modul->jawaban as $key => $row)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td style="font-weight: bold;">{{ $row->user->name }}</td>
                <td>{{ $row->user->email }}</td>
                <td style="text-align: center; font-size: 14px;"><strong>{{ $row->nilai ?? '-' }}</strong></td>
                <td style="font-style: italic; color: #666;">{{ $row->feedback_admin ?? 'Tidak ada catatan' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Civic - Document Management System
    </div>
</body>
</html>
