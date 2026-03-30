<style>
    body { font-family: sans-serif; text-transform: uppercase; font-size: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid black; padding: 8px; text-align: left; }
    .header { text-align: center; border-bottom: 2px solid #800000; padding-bottom: 10px; }
</style>

<div class="header">
    <h2>RENCANA ANGGARAN BIAYA (RAB)</h2>
    <p>{{ $sub->period->name }}</p>
</div>

<p>Nama: {{ $sub->user->name }}</p>
<p>Status: {{ $sub->status }}</p>

<table>
    <thead>
        <tr>
            @foreach($template as $col)
                <th>{{ $col['label'] }}</th>
            @endforeach
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sub->items as $item)
            <tr>
                @foreach($template as $col)
                    <td>{{ $item[$col['label']] ?? '-' }}</td>
                @endforeach
                <td>Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ count($template) }}" style="text-align: right; font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">Rp {{ number_format($sub->total_requested, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
