<x-app-layout>
    <x-slot name="title">Auditoria</x-slot>

    <div class="flex items-center gap-3 mb-6 flex-wrap">
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="action" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Todas as acções</option>
                @foreach(['create'=>'Criação','update'=>'Actualização','delete'=>'Eliminação','view'=>'Consulta'] as $val => $label)
                <option value="{{ $val }}" {{ request('action') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="user_id" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <option value="">Todos os utilizadores</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </form>
        <span class="text-xs text-gray-400 ml-auto">Retenção mínima: 12 meses (NIS2)</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Data/Hora</th>
                    <th class="px-4 py-3 text-left">Utilizador</th>
                    <th class="px-4 py-3 text-left">Acção</th>
                    <th class="px-4 py-3 text-left">Objecto</th>
                    <th class="px-4 py-3 text-left">IP</th>
                    <th class="px-4 py-3 text-left">Detalhe</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                @php
                    $actionColors = [
                        'create' => 'bg-green-100 text-green-700',
                        'update' => 'bg-blue-100 text-blue-700',
                        'delete' => 'bg-red-100 text-red-600',
                        'view'   => 'bg-gray-100 text-gray-600',
                    ];
                    $actionLabels = [
                        'create' => 'Criação',
                        'update' => 'Actualização',
                        'delete' => 'Eliminação',
                        'view'   => 'Consulta',
                    ];
                    $modelName = class_basename($log->auditable_type ?? '');
                    $modelTranslations = [
                        'Client'   => 'Cliente',
                        'Contract' => 'Contrato',
                        'Lead'     => 'Lead',
                        'Proposal' => 'Proposta',
                        'User'     => 'Utilizador',
                        'Plan'     => 'Plano',
                    ];
                @endphp
                <tr class="hover:bg-gray-50" x-data="{open:false}">
                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-4 py-3 font-medium">{{ $log->user?->name ?? 'Sistema' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $actionLabels[$log->action] ?? $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $modelTranslations[$modelName] ?? $modelName }} #{{ $log->auditable_id }}
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $log->ip ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($log->new_values || $log->old_values)
                        <button @click="open=!open" class="text-xs text-indigo-600 hover:text-indigo-800">
                            <span x-text="open ? 'Ocultar' : 'Ver alterações'"></span>
                        </button>
                        <div x-show="open" x-cloak class="mt-2 text-xs text-gray-500 bg-gray-50 rounded p-2 font-mono max-w-xs overflow-auto">
                            @if($log->old_values)
                            <p class="text-red-400 mb-1">— Antes: {{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }}</p>
                            @endif
                            @if($log->new_values)
                            <p class="text-green-600">+ Depois: {{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</p>
                            @endif
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Nenhum registo de auditoria encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-app-layout>
