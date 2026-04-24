<x-app-layout>
    <x-slot name="title">Proposta: {{ $proposal->title }}</x-slot>

    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <a href="{{ route('proposals.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Propostas</a>
            <div class="flex gap-2 flex-wrap">
                @if(in_array($proposal->status, ['draft','sent','viewed']))
                <form method="POST" action="{{ route('proposals.send', $proposal) }}">
                    @csrf
                    <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">
                        {{ $proposal->status === 'draft' ? '📤 Enviar' : '🔁 Re-enviar' }}
                    </button>
                </form>
                @endif
                <a href="{{ route('proposals.pdf', $proposal) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200">⬇ PDF</a>
                <a href="{{ route('proposals.edit', $proposal) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200">Editar</a>
                @if($proposal->status !== 'draft')
                <a href="{{ $proposal->utm_url }}" target="_blank" class="bg-green-50 text-green-700 px-3 py-1.5 rounded-lg text-sm hover:bg-green-100">🔗 Link público</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Main card --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-xl font-bold">{{ $proposal->title }}</h2>
                    @php
                        $sc = ['draft'=>'bg-gray-100 text-gray-600','sent'=>'bg-blue-100 text-blue-700','viewed'=>'bg-purple-100 text-purple-700','accepted'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'];
                        $sl = ['draft'=>'Rascunho','sent'=>'Enviada','viewed'=>'Vista','accepted'=>'Aceite','rejected'=>'Rejeitada'];
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$proposal->status] ?? '' }}">
                        {{ $sl[$proposal->status] ?? $proposal->status }}
                    </span>
                </div>

                <div class="text-sm text-gray-500 space-y-1 mb-5">
                    @if($proposal->client)
                    <p>Cliente: <a href="{{ route('clients.show', $proposal->client) }}" class="text-indigo-600 hover:underline">{{ $proposal->client->name }}</a></p>
                    @endif
                    @if($proposal->lead)
                    <p>Lead: <a href="{{ route('leads.show', $proposal->lead) }}" class="text-indigo-600 hover:underline">{{ $proposal->lead->name }}</a></p>
                    @endif
                    @if($proposal->expires_at)
                    <p class="{{ $proposal->expires_at->isPast() ? 'text-red-500' : '' }}">
                        Válida até: {{ $proposal->expires_at->format('d/m/Y') }}
                        @if($proposal->expires_at->isPast()) <span class="font-semibold">(Expirada)</span>@endif
                    </p>
                    @endif
                </div>

                {{-- Items table --}}
                @if($proposal->items)
                <table class="w-full text-sm mb-4">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Descrição</th>
                            <th class="px-3 py-2 text-right">Qtd</th>
                            <th class="px-3 py-2 text-right">P. Unit.</th>
                            <th class="px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach((array)$proposal->items as $item)
                        <tr>
                            <td class="px-3 py-2">{{ $item['description'] ?? '' }}</td>
                            <td class="px-3 py-2 text-right">{{ $item['qty'] ?? 1 }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }} €</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($item['total'] ?? (($item['qty']??1)*($item['unit_price']??0)), 2, ',', '.') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <div class="flex justify-end">
                    <div class="w-56 space-y-1 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span>{{ number_format($proposal->subtotal, 2, ',', '.') }} €</span>
                        </div>
                        @if($proposal->discount)
                        <div class="flex justify-between text-gray-500">
                            <span>Desconto</span>
                            <span>— {{ number_format($proposal->discount, 2, ',', '.') }} €</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-green-600">{{ number_format($proposal->total, 2, ',', '.') }} €</span>
                        </div>
                    </div>
                </div>

                @if($proposal->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Notas internas</p>
                    <p class="text-sm text-gray-600">{{ $proposal->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">
                {{-- Tracking stats --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">📊 Tracking</h3>
                    @if($proposal->tracking_enabled)
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Visualizações</dt>
                            <dd class="font-semibold">{{ $proposal->trackEvents->count() }}</dd>
                        </div>
                        @if($proposal->sent_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Enviada</dt>
                            <dd>{{ $proposal->sent_at->format('d/m H:i') }}</dd>
                        </div>
                        @endif
                        @if($proposal->viewed_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Primeira vez vista</dt>
                            <dd>{{ $proposal->viewed_at->format('d/m H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                    @if($proposal->trackEvents->count())
                    <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                        @foreach($proposal->trackEvents->take(5) as $event)
                        <div class="text-xs text-gray-400">
                            {{ $event->created_at->format('d/m H:i') }} — {{ Str::limit($event->user_agent, 40) }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @else
                    <p class="text-xs text-gray-400">Tracking desactivado.</p>
                    @endif

                    @if($proposal->status !== 'draft')
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400 mb-1">Link de partilha</p>
                        <div class="flex gap-1">
                            <input type="text" readonly value="{{ $proposal->utm_url }}"
                                   class="flex-1 text-xs border border-gray-200 rounded px-2 py-1 bg-gray-50 truncate">
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $proposal->utm_url }}').then(()=>this.textContent='✓')"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded">Copiar</button>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Timeline --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Histórico</h3>
                    <div class="text-xs text-gray-400 space-y-1">
                        <p>Criada: {{ $proposal->created_at->format('d/m/Y H:i') }} por {{ $proposal->creator?->name }}</p>
                        @if($proposal->sent_at)<p>Enviada: {{ $proposal->sent_at->format('d/m/Y H:i') }}</p>@endif
                        @if($proposal->viewed_at)<p class="text-purple-600">Vista: {{ $proposal->viewed_at->format('d/m/Y H:i') }}</p>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
