<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi - {{ $subProgram->title }}</title>
    <style>
        @page { margin: 1cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.2;
        }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #800000; padding-bottom: 10px; }
        .header h1 { margin: 0; text-transform: uppercase; font-size: 16pt; font-style: italic; font-weight: 900; }
        .header p { margin: 2px 0 0; font-size: 7pt; color: #666; letter-spacing: 3px; font-weight: bold; }

        .info-table { width: 100%; margin-bottom: 15px; font-size: 8pt; border-bottom: 1px solid #eee; padding-bottom: 10px; }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Menjaga lebar kolom tetap konsisten */
        }
        table.main-table th {
            background-color: #800000;
            color: white;
            border: 1px solid #600000;
            padding: 6px 2px;
            font-size: 6.5pt;
            text-transform: uppercase;
        }
        table.main-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
            font-size: 7.5pt;
        }
        .name-cell { text-align: left !important; width: 150px; font-weight: bold; text-transform: uppercase; }
        .rate-cell { background-color: #fffafa; font-weight: bold; width: 50px; color: #800000; }

        .status-present { color: #1D6F42; font-weight: bold; }
        .status-absent { color: #ccc; font-weight: normal; }

        .footer { position: fixed; bottom: -0.5cm; width: 100%; text-align: right; font-size: 6pt; color: #aaa; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Attendance <span style="color: #800000;">Matrix Report</span></h1>
        <p>CIVIC EDUCATIONMANAGEMENT SYSTEM • CIVIC PLATFORM</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="10%"><strong>Program</strong></td>
            <td width="40%">: {{ $subProgram->program->title }}</td>
            <td width="10%"><strong>Generated</strong></td>
            <td width="40%">: {{ $date }}</td>
        </tr>
        <tr>
            <td><strong>SubProgram</strong></td>
            <td>: {{ $subProgram->title }}</td>
            <td><strong>Total Slots</strong></td>
            <td>: {{ $subProgram->absensis->count() }} Pertemuan</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th class="name-cell">Mahasiswa / NIM</th>
                @foreach($subProgram->absensis as $absen)
                    <th>{{ Str::limit($absen->title, 10) }}</th>
                @endforeach
                <th class="rate-cell">Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSlots = $subProgram->absensis->count(); @endphp
            @foreach($users as $index => $user)
                @php $userAttendanceCount = 0; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="name-cell">
                        {{ $user->name }}<br>
                        <span style="font-size: 6pt; color: #777; font-weight: normal;">NIM: {{ $user->nim ?? '-' }}</span>
                    </td>

                    @foreach($subProgram->absensis as $absen)
                        @php
                            $hadir = $absen->kehadirans->where('user_id', $user->id)->first();
                            if($hadir) $userAttendanceCount++;
                        @endphp
                        <td>
                            @if($hadir)
                                <span class="status-present">V</span>
                            @else
                                <span class="status-absent">0</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="rate-cell">
                        {{ $totalSlots > 0 ? round(($userAttendanceCount / $totalSlots) * 100) : 0 }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document ID: {{ md5($subProgram->id . now()) }} • Printed by Admin Civic
    </div>

</body>
</html>
