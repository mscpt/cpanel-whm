<x-app-layout>
    <x-slot name="title">Mapeamento de Colunas CSV</x-slot>

    <div class="max-w-3xl">
        <a href="{{ route('import.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Importar</a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
            <h2 class="text-lg font-semibold mb-1">Mapeamento de Colunas</h2>
            <p class="text-sm text-gray-500 mb-6">
                Associe as colunas do CSV do WHMCS aos campos do CRM. O sistema detectou as seguintes colunas.
            </p>

            <form method="POST" action="{{ route('import.process') }}">
                @csrf

                {{-- Column mapping --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Coluna do CSV</th>
                                <th class="px-4 py-3 text-left">Campo no CRM</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($headers as $header)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5 font-mono text-xs text-gray-600">{{ $header }}</td>
                                <td class="px-4 py-2.5">
                                    <select name="mapping[{{ $header }}]"
                                            class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">— Ignorar —</option>
                                        @foreach($crmFields as $fieldKey => $fieldLabel)
                                        <option value="{{ $fieldKey }}"
                                                {{ ($suggested[$header] ?? '') === $fieldKey ? 'selected' : '' }}>
                                            {{ $fieldLabel }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Stage & duplicate options --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etapa de destino *</label>
                        <select name="pipeline_stage_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($pipeline->stages->sortBy('order') as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duplicados (por email)</label>
                        <select name="on_duplicate"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="skip">Ignorar</option>
                            <option value="update">Actualizar</option>
                        </select>
                    </div>
                </div>

                {{-- Preview --}}
                @if(count($rows))
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Pré-visualização (primeiras {{ count($rows) }} linhas)</p>
                    <div class="border border-gray-200 rounded-lg overflow-x-auto">
                        <table class="text-xs whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 uppercase">
                                <tr>
                                    @foreach($headers as $h)
                                    <th class="px-3 py-2 text-left">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($rows as $row)
                                <tr>
                                    @foreach($row as $cell)
                                    <td class="px-3 py-2 text-gray-600 max-w-[160px] truncate">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="flex gap-3">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                        Importar Agora
                    </button>
                    <a href="{{ route('import.index') }}" class="px-5 py-2 rounded-lg text-sm bg-gray-100 hover:bg-gray-200">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
