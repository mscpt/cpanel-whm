<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    <div class="flex items-center justify-between mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou email..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button class="bg-gray-100 px-3 py-2 rounded-lg text-sm hover:bg-gray-200">Pesquisar</button>
        </form>
        <a href="{{ route('clients.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Novo Cliente</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Telefone</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Cidade</th>
                    <th class="px-4 py-3 text-left">Criado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($clients as $client)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">
                        <a href="{{ route('clients.show', $client) }}" class="text-indigo-600 hover:underline">{{ $client->name }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $client->email }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $client->phone }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $client->type === 'company' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $client->type === 'company' ? 'Empresa' : 'Pessoa' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $client->city }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $client->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('clients.edit', $client) }}" class="text-gray-400 hover:text-indigo-600 text-xs mr-2">Editar</a>
                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Arquivar cliente?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-600 text-xs">Arquivar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhum cliente encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $clients->links() }}</div>
</x-app-layout>
