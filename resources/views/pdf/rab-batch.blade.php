<!DOCTYPE html>
<html>
<head>
    <title>Rekap RAB - {{ $period->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        .header { text-align: center; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>
    <div class="header">LAPORAN REKAP RAB: {{ $period->name }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Total Budget</th>
                </tr>
        </thead>
        <tbody>
            @foreach($submissions as $index => $sub)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sub->user->name }}</td>
                <td>Rp {{ number_format($sub->total_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
