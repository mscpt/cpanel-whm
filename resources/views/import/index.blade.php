<x-app-layout>
    <x-slot name="title">Importar CSV (WHMCS)</x-slot>

    <div class="max-w-2xl">
        {{-- Upload form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-1">Importar Leads via CSV</h2>
            <p class="text-sm text-gray-500 mb-5">
                Faça upload de um ficheiro CSV exportado do WHMCS. O sistema irá sugerir o mapeamento das colunas automaticamente.
            </p>

            <form method="POST" action="{{ route('import.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ficheiro CSV *</label>
                        <input type="file" name="file" accept=".csv,.txt" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 mt-1">Máximo 10 MB · .csv ou .txt</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pipeline de destino *</label>
                        <select name="pipeline_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Seleccionar pipeline...</option>
                            @foreach($pipelines as $pipeline)
                            <option value="{{ $pipeline->id }}">{{ $pipeline->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">
                        Avançar →
                    </button>
                </div>
            </form>
        </div>

        {{-- Import history --}}
        @if($imports->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Histórico de Importações</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Data</th>
                        <th class="px-4 py-3 text-left">Utilizador</th>
                        <th class="px-4 py-3 text-right">Criados</th>
                        <th class="px-4 py-3 text-right">Actualizados</th>
                        <th class="px-4 py-3 text-right">Ignorados</th>
                        <th class="px-4 py-3 text-right">Erros</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($imports as $import)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $import->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $import->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-green-600 font-semibold">{{ $import->created }}</td>
                        <td class="px-4 py-3 text-right text-blue-600">{{ $import->updated }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">{{ $import->skipped }}</td>
                        <td class="px-4 py-3 text-right text-red-500">{{ $import->errors }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>
