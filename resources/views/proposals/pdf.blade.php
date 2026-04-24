<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $proposal->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 20px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #f3f4f6; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #6b7280; }
        td { padding: 8px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .totals { float: right; width: 220px; margin-top: 12px; }
        .totals td { border: none; padding: 4px 8px; }
        .total-row { font-weight: bold; font-size: 14px; border-top: 2px solid #e5e7eb; }
        .header { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 10px; background: #e0e7ff; color: #4338ca; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <strong style="font-size:16px;color:#4338ca;">{{ \App\Models\Setting::get('company_name', 'Webhs') }}</strong><br>
            <span style="color:#6b7280;">{{ \App\Models\Setting::get('company_email') }}</span><br>
            <span style="color:#6b7280;">{{ \App\Models\Setting::get('company_phone') }}</span>
        </div>
        <div style="text-align:right;">
            <div style="color:#6b7280;font-size:10px;">Proposta nº</div>
            <div style="font-size:22px;font-weight:bold;">#{{ str_pad($proposal->id, 4, '0', STR_PAD_LEFT) }}</div>
            @if($proposal->expires_at)
            <div style="color:#6b7280;font-size:10px;">Válida até {{ $proposal->expires_at->format('d/m/Y') }}</div>
            @endif
        </div>
    </div>

    <h1>{{ $proposal->title }}</h1>
    @if($proposal->client || $proposal->lead)
    <p style="color:#6b7280;">Para: <strong>{{ $proposal->client?->name ?? $proposal->lead?->name }}</strong></p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th class="text-right" style="width:60px;">Qtd</th>
                <th class="text-right" style="width:90px;">P. Unit.</th>
                <th class="text-right" style="width:90px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach((array)$proposal->items as $item)
            <tr>
                <td>{{ $item['description'] ?? '' }}</td>
                <td class="text-right">{{ $item['qty'] ?? 1 }}</td>
                <td class="text-right">{{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }} €</td>
                <td class="text-right"><strong>{{ number_format($item['total'] ?? (($item['qty']??1)*($item['unit_price']??0)), 2, ',', '.') }} €</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="text-right">{{ number_format($proposal->subtotal, 2, ',', '.') }} €</td></tr>
        @if($proposal->discount)
        <tr><td>Desconto</td><td class="text-right">— {{ number_format($proposal->discount, 2, ',', '.') }} €</td></tr>
        @endif
        <tr class="total-row"><td>Total</td><td class="text-right">{{ number_format($proposal->total, 2, ',', '.') }} €</td></tr>
    </table>

    <div style="clear:both;"></div>

    @if($proposal->notes)
    <div style="margin-top:24px;padding:12px;background:#f9fafb;border-radius:4px;">
        <strong>Notas:</strong><br>{{ $proposal->notes }}
    </div>
    @endif

    <div class="footer">
        {{ \App\Models\Setting::get('company_name', 'Webhs') }} · {{ \App\Models\Setting::get('company_address') }} · {{ \App\Models\Setting::get('company_email') }}<br>
        Documento gerado em {{ now()->format('d/m/Y') }}.
    </div>
</body>
</html>
