<x-app-layout>
    <x-slot name="title">Etapas — {{ $pipeline->name }}</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('pipelines.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Pipelines</a>

        <div class="flex items-center justify-between mt-4 mb-6">
            <h2 class="text-xl font-bold">Pipeline: {{ $pipeline->name }}</h2>
            <a href="{{ route('leads.index', ['pipeline_id' => $pipeline->id]) }}" class="text-sm bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-100">
                Ver Board →
            </a>
        </div>

        {{-- Stages list (sortable) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6" id="stages-list">
            @forelse($pipeline->stages->sortBy('order') as $stage)
            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50" data-stage-id="{{ $stage->id }}">
                <span class="cursor-grab text-gray-300 hover:text-gray-500 text-lg">⠿</span>
                <div class="w-4 h-4 rounded-full shrink-0" style="background-color:{{ $stage->color }}"></div>

                <div class="flex-1" x-data="{editing:false}">
                    <div x-show="!editing" class="flex items-center gap-2">
                        <span class="text-sm font-medium">{{ $stage->name }}</span>
                        @if($stage->is_won) <span class="text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded">Ganho</span>@endif
                        @if($stage->is_lost) <span class="text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Perdido</span>@endif
                        <button @click="editing=true" class="text-xs text-gray-400 hover:text-indigo-600 ml-2">Editar</button>
                    </div>
                    <form x-show="editing" x-cloak method="POST" action="{{ route('pipeline-stages.update', $stage) }}" class="flex items-center gap-2 flex-wrap">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $stage->name }}" required
                               class="border border-gray-300 rounded-lg px-2 py-1 text-sm w-40 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <input type="color" name="color" value="{{ $stage->color }}"
                               class="w-8 h-8 rounded cursor-pointer border border-gray-200">
                        <label class="flex items-center gap-1 text-xs text-gray-600">
                            <input type="checkbox" name="is_won" value="1" {{ $stage->is_won ? 'checked' : '' }}> Ganho
                        </label>
                        <label class="flex items-center gap-1 text-xs text-gray-600">
                            <input type="checkbox" name="is_lost" value="1" {{ $stage->is_lost ? 'checked' : '' }}> Perdido
                        </label>
                        <button type="submit" class="bg-indigo-600 text-white px-2 py-1 rounded text-xs hover:bg-indigo-700">✓</button>
                        <button type="button" @click="editing=false" class="text-gray-400 text-xs hover:text-gray-600">✕</button>
                    </form>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs text-gray-400">{{ $stage->leads->count() }} leads</span>
                    <form method="POST" action="{{ route('pipeline-stages.destroy', $stage) }}" onsubmit="return confirm('Eliminar etapa?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-300 hover:text-red-500 text-sm">✕</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-4 py-6 text-center text-gray-400 text-sm">Nenhuma etapa configurada.</div>
            @endforelse
        </div>

        {{-- Add stage --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-data="{open:false}">
            <button @click="open=!open" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">
                <span x-text="open?'−':'+'"></span> Adicionar Etapa
            </button>
            <form x-show="open" x-cloak method="POST" action="{{ route('pipeline-stages.store', $pipeline) }}" class="mt-4">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                        <input type="text" name="name" required placeholder="Ex: Em negociação"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                        <input type="color" name="color" value="#6366f1"
                               class="w-full h-10 rounded-lg cursor-pointer border border-gray-200">
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="is_won" value="1" class="rounded border-gray-300"> Etapa de Ganho
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="is_lost" value="1" class="rounded border-gray-300"> Etapa de Perda
                        </label>
                    </div>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Adicionar</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const list = document.getElementById('stages-list');
        if (!list) return;
        new Sortable(list, {
            animation: 150,
            handle: '[data-stage-id]',
            ghostClass: 'opacity-50',
            onEnd: function () {
                const order = [...list.querySelectorAll('[data-stage-id]')].map(el => parseInt(el.dataset.stageId));
                fetch('{{ route('pipeline-stages.reorder', $pipeline) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order })
                });
            }
        });
    });
    </script>
</x-app-layout>
