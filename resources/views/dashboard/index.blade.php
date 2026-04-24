<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Clientes',           'value' => $stats['clients'],          'color' => 'indigo', 'link' => route('clients.index')],
            ['label' => 'Contratos Activos',  'value' => $stats['active_contracts'], 'color' => 'green',  'link' => route('contracts.index', ['status' => 'active'])],
            ['label' => 'Leads em Aberto',    'value' => $stats['open_leads'],       'color' => 'amber',  'link' => route('leads.index')],
            ['label' => 'Propostas Enviadas', 'value' => $stats['proposals_sent'],   'color' => 'blue',   'link' => route('proposals.index')],
        ] as $stat)
        <a href="{{ $stat['link'] }}" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
            <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
            <p class="text-3xl font-bold text-{{ $stat['color'] }}-600 mt-1">{{ $stat['value'] }}</p>
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upcoming renewals --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Renovações nos próximos 60 dias</h2>
            @forelse($renewals as $contract)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <div>
                    <a href="{{ route('clients.show', $contract->client) }}" class="text-sm font-medium text-indigo-600 hover:underline">{{ $contract->client->name }}</a>
                    <p class="text-xs text-gray-400">{{ $contract->title ?? ($contract->plan?->name ?? 'Contrato #'.$contract->id) }}</p>
                </div>
                <span class="text-sm {{ $contract->renewal_date->isPast() ? 'text-red-600 font-bold' : ($contract->renewal_date->diffInDays() < 14 ? 'text-orange-500 font-semibold' : 'text-gray-500') }}">
                    {{ $contract->renewal_date->format('d/m/Y') }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-400">Sem renovações próximas.</p>
            @endforelse
        </div>

        {{-- Recent activities --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Atividade Recente</h2>
            @forelse($recentActivities as $activity)
            <div class="flex gap-3 py-2 border-b border-gray-50 last:border-0">
                <span class="text-lg leading-none mt-0.5">
                    {{ ['note' => '📝', 'call' => '📞', 'email' => '✉️', 'meeting' => '📅'][$activity->type] ?? '•' }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-700 truncate">{{ $activity->subject ?? $activity->description }}</p>
                    <p class="text-xs text-gray-400">{{ $activity->user->name }} · {{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Sem atividade recente.</p>
            @endforelse
        </div>
    </div>

    {{-- Pipeline summary --}}
    @foreach($pipelines as $pipeline)
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-700">Pipeline: {{ $pipeline->name }}</h2>
            <a href="{{ route('leads.index', ['pipeline_id' => $pipeline->id]) }}" class="text-sm text-indigo-600 hover:underline">Ver board →</a>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2">
            @foreach($pipeline->stages as $stage)
            <div class="flex-shrink-0 text-center">
                <div class="w-4 h-4 rounded-full mx-auto mb-1" style="background-color: {{ $stage->color }}"></div>
                <p class="text-xs text-gray-500">{{ $stage->name }}</p>
                <p class="text-lg font-bold" style="color: {{ $stage->color }}">{{ $stage->leads->count() }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</x-app-layout>
