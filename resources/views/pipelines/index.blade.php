<x-app-layout>
    <x-slot name="title">Pipelines</x-slot>

    <div class="max-w-4xl">
        {{-- Create pipeline --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6" x-data="{open:false}">
            <button @click="open=!open" class="flex items-center gap-2 text-indigo-600 text-sm font-medium hover:text-indigo-800">
                <span x-text="open ? '−' : '+'"></span> Novo Pipeline
            </button>
            <form x-show="open" x-cloak method="POST" action="{{ route('pipelines.store') }}" class="mt-4 grid grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                    <input type="text" name="name" required placeholder="Ex: Vendas, Renovações..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <input type="text" name="description"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="col-span-2">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Criar Pipeline</button>
                </div>
            </form>
        </div>

        {{-- Pipelines list --}}
        <div class="space-y-4">
            @forelse($pipelines as $pipeline)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-data="{editing:false}">
                <div class="flex items-start justify-between">
                    <div>
                        <div x-show="!editing">
                            <h3 class="font-semibold text-gray-800">{{ $pipeline->name }}</h3>
                            @if($pipeline->description)
                            <p class="text-sm text-gray-500">{{ $pipeline->description }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $pipeline->leads_count }} lead(s) · {{ $pipeline->stages->count() }} etapas</p>
                        </div>
                        <form x-show="editing" x-cloak method="POST" action="{{ route('pipelines.update', $pipeline) }}" class="flex gap-2 items-center">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $pipeline->name }}" required
                                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <input type="text" name="description" value="{{ $pipeline->description }}" placeholder="Descrição"
                                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                            <button type="button" @click="editing=false" class="text-gray-400 text-sm hover:text-gray-600">Cancelar</button>
                        </form>
                    </div>
                    <div class="flex gap-2 shrink-0 ml-4">
                        <a href="{{ route('pipelines.stages', $pipeline) }}" class="text-xs bg-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-200">⚙ Etapas</a>
                        <a href="{{ route('leads.index', ['pipeline_id' => $pipeline->id]) }}" class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-lg hover:bg-indigo-100">Ver Board</a>
                        <button @click="editing=!editing" class="text-xs text-gray-400 hover:text-indigo-600">Editar</button>
                        <form method="POST" action="{{ route('pipelines.destroy', $pipeline) }}" onsubmit="return confirm('Eliminar pipeline?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-gray-400 hover:text-red-600">Eliminar</button>
                        </form>
                    </div>
                </div>

                {{-- Stage pills --}}
                <div class="flex gap-2 mt-3 flex-wrap">
                    @foreach($pipeline->stages->sortBy('order') as $stage)
                    <span class="flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                        <span class="w-2 h-2 rounded-full inline-block" style="background-color:{{ $stage->color }}"></span>
                        {{ $stage->name }}
                        @if($stage->is_won) <span class="text-green-600">✓</span>@endif
                        @if($stage->is_lost) <span class="text-red-500">✗</span>@endif
                    </span>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400">
                Nenhum pipeline criado ainda. Crie o primeiro acima.
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
