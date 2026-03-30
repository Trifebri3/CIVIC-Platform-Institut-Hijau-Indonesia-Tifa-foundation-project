<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 30px; }
        .label { font-weight: bold; font-size: 10px; text-transform: uppercase; color: #888; margin-top: 20px; }
        .value { font-size: 14px; margin-top: 5px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; font-size: 11px; }
        th { bg-color: #f5f5f5; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>TERM OF REFERENCE (TOR)</h2>
        <p>{{ $submission->period->name }} - {{ $submission->user->name }}</p>
    </div>

    @foreach($template as $field)
        <div class="field-group">
            <div class="label">{{ $field['label'] }}</div>

            <div class="value">
                @php $val = $answers[$field['id']] ?? '-'; @endphp

                @if($field['type'] === 'table')
                    <table>
                        <thead>
                            <tr>
                                @foreach($field['columns'] as $col)
                                    <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            {{-- User menyimpan jawaban tabel sebagai array of objects --}}
                            @isset($answers[$field['id']])
                                @foreach($answers[$field['id']] as $row)
                                    <tr>
                                        @foreach($field['columns'] as $col)
                                            <td>{{ $row[$col] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                @elseif($field['type'] === 'richtext')
                    {!! $val !!}
                @else
                    {{ $val }}
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>
