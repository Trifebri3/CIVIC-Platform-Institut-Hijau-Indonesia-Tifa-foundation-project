<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #800000; padding-bottom: 10px; }
        .header h2 { color: #800000; margin: 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #800000; color: white; padding: 10px; text-align: left; text-transform: uppercase; font-size: 8pt; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .footer { margin-top: 30px; text-align: right; font-style: italic; font-size: 8pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Tanggal Laporan: {{ $date }} | CIVIC Education Platform</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                @foreach($templates as $template)
                    <th>{{ $template->field_label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                @foreach($templates as $template)
                    <td>{{ $user->profile->custom_fields_values[$template->field_name] ?? '-' }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem CIVIC pada {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
