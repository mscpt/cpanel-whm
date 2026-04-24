<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $proposal->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @if($proposal->tracking_enabled)
    <img src="{{ route('track.pixel', $proposal->token) }}" width="1" height="1" alt="" style="position:absolute;opacity:0;">
    @endif
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-3xl mx-auto py-10 px-4">

        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
            <div class="flex items-start justify-between mb-8">
                <div>
                    @if(\App\Models\Setting::get('company_logo_url'))
                    <img src="{{ \App\Models\Setting::get('company_logo_url') }}" alt="Logo" class="h-10 mb-3">
                    @else
                    <p class="text-xl font-bold text-indigo-700">{{ \App\Models\Setting::get('company_name', 'Webhs') }}</p>
                    @endif
                    <p class="text-sm text-gray-500">{{ \App\Models\Setting::get('company_email') }}</p>
                    <p class="text-sm text-gray-500">{{ \App\Models\Setting::get('company_phone') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Proposta nº</p>
                    <p class="text-2xl font-bold text-gray-800">#{{ str_pad($proposal->id, 4, '0', STR_PAD_LEFT) }}</p>
                    @if($proposal->expires_at)
                    <p class="text-xs text-gray-400 mt-1">Válida até {{ $proposal->expires_at->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>

            <h1 class="text-2xl font-bold mb-2">{{ $proposal->title }}</h1>

            @if($proposal->client || $proposal->lead)
            <div class="bg-gray-50 rounded-xl p-4 text-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Para</p>
                <p class="font-semibold text-gray-800">{{ $proposal->client?->name ?? $proposal->lead?->name }}</p>
                @if($proposal->client?->email ?? $proposal->lead?->email)
                <p class="text-gray-500">{{ $proposal->client?->email ?? $proposal->lead?->email }}</p>
                @endif
            </div>
            @endif
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6">
            <h2 class="text-lg font-semibold mb-4">Detalhes da Proposta</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-200 text-left text-gray-500 text-xs uppercase">
                        <th class="pb-2">Descrição</th>
                        <th class="pb-2 text-right">Qtd</th>
                        <th class="pb-2 text-right">P. Unit.</th>
                        <th class="pb-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach((array)$proposal->items as $item)
                    <tr class="border-b border-gray-50">
                        <td class="py-3">{{ $item['description'] ?? '' }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $item['qty'] ?? 1 }}</td>
                        <td class="py-3 text-right text-gray-600">{{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }} €</td>
                        <td class="py-3 text-right font-semibold">{{ number_format($item['total'] ?? (($item['qty']??1)*($item['unit_price']??0)), 2, ',', '.') }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end mt-6">
                <div class="w-60 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal</span>
                        <span>{{ number_format($proposal->subtotal, 2, ',', '.') }} €</span>
                    </div>
                    @if($proposal->discount)
                    <div class="flex justify-between text-gray-500">
                        <span>Desconto</span>
                        <span class="text-red-500">— {{ number_format($proposal->discount, 2, ',', '.') }} €</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-lg pt-2 border-t-2 border-gray-200">
                        <span>Total</span>
                        <span class="text-indigo-700">{{ number_format($proposal->total, 2, ',', '.') }} €</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accept --}}
        @if($proposal->status !== 'accepted' && $proposal->status !== 'rejected')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-6 text-center">
            <p class="text-gray-600 mb-4">Concorda com esta proposta?</p>
            <form method="POST" action="{{ route('proposals.accept', $proposal->token) }}">
                @csrf
                <button type="submit"
                        class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                    ✓ Aceitar Proposta
                </button>
            </form>
        </div>
        @elseif($proposal->status === 'accepted')
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6 text-center text-green-700 mb-6">
            <p class="text-lg font-semibold">✓ Proposta Aceite</p>
            <p class="text-sm mt-1">Obrigado! Entraremos em contacto brevemente.</p>
        </div>
        @endif

        {{-- Footer RGPD --}}
        <div class="text-center text-xs text-gray-400 mt-8 px-4">
            <p>Esta proposta foi enviada por {{ \App\Models\Setting::get('company_name', 'Webhs') }} · {{ \App\Models\Setting::get('company_address') }}</p>
            <p class="mt-1">Esta página pode registar a sua visualização para fins de acompanhamento comercial (RGPD Art.º 6.º n.º 1 al. f)).</p>
        </div>
    </div>
</body>
</html>
