<x-app-layout>
    <x-slot name="title">Contratos</x-slot>
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar cliente..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Todos</option>
                <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Activo</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </form>
        <a href="{{ route('contracts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Novo Contrato</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr><th class="px-4 py-3 text-left">Cliente</th><th class="px-4 py-3 text-left">Plano</th><th class="px-4 py-3 text-left">Valor</th><th class="px-4 py-3 text-left">Renovação</th><th class="px-4 py-3 text-left">Estado</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium"><a href="{{ route('clients.show', $contract->client) }}" class="text-indigo-600 hover:underline">{{ $contract->client->name }}</a></td>
                    <td class="px-4 py-3 text-gray-500">{{ $contract->plan?->name ?? $contract->title ?? '—' }}</td>
                    <td class="px-4 py-3">{{ number_format($contract->value, 2, ',', '.') }} €</td>
                    <td class="px-4 py-3 {{ $contract->renewal_date && $contract->renewal_date->isPast() ? 'text-red-600 font-semibold' : ($contract->renewal_date && $contract->renewal_date->diffInDays() < 30 ? 'text-orange-500' : 'text-gray-500') }}">
                        {{ $contract->renewal_date?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ ['active'=>'bg-green-100 text-green-700','suspended'=>'bg-yellow-100 text-yellow-700','cancelled'=>'bg-red-100 text-red-700'][$contract->status] ?? '' }}">
                            {{ ucfirst($contract->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('contracts.show', $contract) }}" class="text-xs text-indigo-600 hover:underline mr-2">Ver</a>
                        <a href="{{ route('contracts.edit', $contract) }}" class="text-xs text-gray-400 hover:text-indigo-600">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Nenhum contrato encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $contracts->links() }}</div>
</x-app-layout>
