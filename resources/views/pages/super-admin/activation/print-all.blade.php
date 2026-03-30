<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #008080; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: top; }
        th { background-color: #008080; color: white; text-transform: uppercase; }
        .user-info { font-weight: bold; color: #008080; }
        .footer { margin-top: 30px; text-align: right; font-style: italic; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN REKAPITULASI AKTIVASI USER</h2>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama User</th>
                <th>Jawaban Aktivasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <span class="user-info">{{ $user->name }}</span><br>
                    <small>{{ $user->email }}</small>
                </td>
                <td>
                    <ul style="padding-left: 15px; margin: 0;">
                        @foreach($user->activationAnswers as $ans)
                            <li>
                                <strong>{{ $ans->question->title }}:</strong>
                                @if(is_array($ans->content))
                                    {{ implode(', ', $ans->content) }}
                                @else
                                    {{ $ans->content }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Yota Adiwidya Center
    </div>
</body>
</html>
