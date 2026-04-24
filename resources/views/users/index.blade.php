<x-app-layout>
    <x-slot name="title">Utilizadores</x-slot>

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Gerir utilizadores internos do CRM</p>
        <a href="{{ route('users.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Novo Utilizador</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Perfil</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Último acesso</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 {{ !$user->is_active ? 'opacity-50' : '' }}">
                    <td class="px-4 py-3 font-medium">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                        <span class="text-xs text-indigo-500 ml-1">(você)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $user->role === 'admin' ? 'Admin' : 'Comercial' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $user->last_login_at?->diffForHumans() ?? 'Nunca' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('users.edit', $user) }}" class="text-xs text-gray-400 hover:text-indigo-600 mr-2">Editar</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Remover utilizador?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-gray-400 hover:text-red-600">Remover</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Nenhum utilizador encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
