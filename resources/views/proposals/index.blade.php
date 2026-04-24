<x-app-layout>
    <x-slot name="title">Propostas</x-slot>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Todos os estados</option>
                @foreach(['draft'=>'Rascunho','sent'=>'Enviada','viewed'=>'Vista','accepted'=>'Aceite','rejected'=>'Rejeitada'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('proposals.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Nova Proposta</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Cliente / Lead</th>
                    <th class="px-4 py-3 text-left">Total</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Enviada</th>
                    <th class="px-4 py-3 text-left">Vista</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($proposals as $proposal)
                @php
                    $statusColors = [
                        'draft'    => 'bg-gray-100 text-gray-600',
                        'sent'     => 'bg-blue-100 text-blue-700',
                        'viewed'   => 'bg-purple-100 text-purple-700',
                        'accepted' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                    $statusLabels = [
                        'draft'    => 'Rascunho',
                        'sent'     => 'Enviada',
                        'viewed'   => 'Vista',
                        'accepted' => 'Aceite',
                        'rejected' => 'Rejeitada',
                    ];
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">
                        <a href="{{ route('proposals.show', $proposal) }}" class="text-indigo-600 hover:underline">{{ $proposal->title }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $proposal->client?->name ?? $proposal->lead?->name ?? '—' }}</td>
                    <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($proposal->total, 2, ',', '.') }} €</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $statusColors[$proposal->status] ?? '' }}">
                            {{ $statusLabels[$proposal->status] ?? $proposal->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $proposal->sent_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $proposal->viewed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('proposals.show', $proposal) }}" class="text-xs text-indigo-600 hover:underline mr-2">Ver</a>
                        <a href="{{ route('proposals.edit', $proposal) }}" class="text-xs text-gray-400 hover:text-indigo-600">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhuma proposta encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $proposals->links() }}</div>
</x-app-layout>
