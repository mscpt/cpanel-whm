<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>

    <div class="flex items-start justify-between mb-6">
        <div>
            <a href="{{ route('clients.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Clientes</a>
            <h2 class="text-2xl font-bold mt-1">{{ $client->name }}</h2>
            <p class="text-sm text-gray-500">{{ $client->email }} {{ $client->phone ? '· '.$client->phone : '' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('contracts.create', ['client_id' => $client->id]) }}" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-green-700">+ Contrato</a>
            <a href="{{ route('proposals.create', ['client_id' => $client->id]) }}" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">+ Proposta</a>
            <a href="{{ route('clients.edit', $client) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200">Editar</a>
            <a href="{{ route('clients.export', $client) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200">Exportar RGPD</a>
        </div>
    </div>

    <div x-data="{ tab: 'contacts' }">
        {{-- Tabs --}}
        <div class="flex gap-1 border-b border-gray-200 mb-6">
            @foreach([['contacts', 'Contactos ('.count($client->contacts).')'],['contracts','Contratos ('.count($client->contracts).')'],['activities','Timeline ('.count($client->activities).')']] as [$key, $label])
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm font-medium -mb-px">{{ $label }}</button>
            @endforeach
        </div>

        {{-- Contacts tab --}}
        <div x-show="tab === 'contacts'">
            <div class="flex justify-end mb-3">
                <a href="{{ route('contacts.create', $client) }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">+ Contacto</a>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr><th class="px-4 py-3 text-left">Nome</th><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-left">Telefone</th><th class="px-4 py-3 text-left">Cargo</th><th class="px-4 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($client->contacts as $contact)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $contact->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $contact->email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $contact->phone }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $contact->role }}</td>
                            <td class="px-4 py-3 text-right gap-2 flex justify-end">
                                <a href="{{ route('contacts.edit', $contact) }}" class="text-xs text-gray-400 hover:text-indigo-600">Editar</a>
                                <form method="POST" action="{{ route('contacts.destroy', $contact) }}" onsubmit="return confirm('Remover?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-gray-400 hover:text-red-600">Remover</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sem contactos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Contracts tab --}}
        <div x-show="tab === 'contracts'" x-cloak>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr><th class="px-4 py-3 text-left">Plano</th><th class="px-4 py-3 text-left">Valor</th><th class="px-4 py-3 text-left">Renovação</th><th class="px-4 py-3 text-left">Estado</th><th class="px-4 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($client->contracts as $contract)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $contract->plan?->name ?? $contract->title ?? '#'.$contract->id }}</td>
                            <td class="px-4 py-3">{{ number_format($contract->value, 2, ',', '.') }} €</td>
                            <td class="px-4 py-3 text-gray-500">{{ $contract->renewal_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ ['active'=>'bg-green-100 text-green-700','suspended'=>'bg-yellow-100 text-yellow-700','cancelled'=>'bg-red-100 text-red-700'][$contract->status] ?? '' }}">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('contracts.show', $contract) }}" class="text-xs text-indigo-600 hover:underline">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sem contratos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Activities tab --}}
        <div x-show="tab === 'activities'" x-cloak>
            @include('activities._form', ['activityableType' => 'client', 'activityableId' => $client->id])
            <div class="mt-4 space-y-3">
                @forelse($client->activities as $activity)
                <div class="bg-white rounded-xl border border-gray-100 p-4 flex gap-3">
                    <span class="text-xl">{{ ['note'=>'📝','call'=>'📞','email'=>'✉️','meeting'=>'📅'][$activity->type] ?? '•' }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ $activity->subject }}</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $activity->description }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $activity->user->name }} · {{ $activity->occurred_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <form method="POST" action="{{ route('activities.destroy', $activity) }}" onsubmit="return confirm('Remover?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-300 hover:text-red-500 text-xs">×</button>
                    </form>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-6">Sem atividades registadas.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <div class="mt-8 pt-6 border-t border-gray-100">
        <p class="text-xs text-gray-400 mb-2">Zona RGPD</p>
        <form method="POST" action="{{ route('clients.anonymize', $client) }}" onsubmit="return confirm('Anonimizar dados deste cliente? Esta acção não pode ser revertida.')">
            @csrf
            <button class="text-xs text-red-500 hover:text-red-700">Anonimizar dados (RGPD)</button>
        </form>
    </div>
    @endif
</x-app-layout>
