<x-app-layout>
    <x-slot name="title">Contrato #{{ $contract->id }}</x-slot>

    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('contracts.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Contratos</a>
            <div class="flex gap-2">
                <a href="{{ route('contracts.edit', $contract) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200">Editar</a>
                <form method="POST" action="{{ route('contracts.destroy', $contract) }}" onsubmit="return confirm('Arquivar contrato?')">
                    @csrf @method('DELETE')
                    <button class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-sm hover:bg-red-100">Arquivar</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold">{{ $contract->title ?? ($contract->plan?->name ?? 'Contrato #'.$contract->id) }}</h2>
                    <a href="{{ route('clients.show', $contract->client) }}" class="text-indigo-600 hover:underline text-sm">{{ $contract->client->name }}</a>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ ['active'=>'bg-green-100 text-green-700','suspended'=>'bg-yellow-100 text-yellow-700','cancelled'=>'bg-red-100 text-red-700'][$contract->status] ?? '' }}">
                    {{ ucfirst($contract->status) }}
                </span>
            </div>

            <dl class="grid grid-cols-2 gap-x-8 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">Plano</dt>
                    <dd class="font-medium">{{ $contract->plan?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Valor</dt>
                    <dd class="font-medium text-green-600">{{ number_format($contract->value, 2, ',', '.') }} €</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Faturação</dt>
                    <dd class="font-medium">{{ ['monthly'=>'Mensal','yearly'=>'Anual','custom'=>'Personalizado'][$contract->billing_cycle] ?? $contract->billing_cycle }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Início</dt>
                    <dd class="font-medium">{{ $contract->start_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Fim</dt>
                    <dd class="font-medium">{{ $contract->end_date?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Renovação</dt>
                    <dd class="font-medium {{ $contract->renewal_date && $contract->renewal_date->isPast() ? 'text-red-600' : ($contract->renewal_date && $contract->renewal_date->diffInDays() < 30 ? 'text-orange-500' : '') }}">
                        {{ $contract->renewal_date?->format('d/m/Y') ?? '—' }}
                        @if($contract->renewal_date && !$contract->renewal_date->isPast())
                            <span class="text-xs text-gray-400">(em {{ $contract->renewal_date->diffInDays() }} dias)</span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if($contract->notes)
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Notas</p>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $contract->notes }}</p>
            </div>
            @endif

            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-400">
                Criado em {{ $contract->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</x-app-layout>
