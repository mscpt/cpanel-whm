<x-app-layout>
    <x-slot name="title">Pipeline — {{ $pipeline->name }}</x-slot>

    <div class="flex items-center gap-4 mb-6 flex-wrap">
        {{-- Pipeline selector --}}
        <div class="flex gap-2">
            @foreach($pipelines as $p)
            <a href="{{ route('leads.index', ['pipeline_id' => $p->id]) }}"
               class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                      {{ $p->id === $pipeline->id ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                {{ $p->name }}
            </a>
            @endforeach
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex gap-2 ml-auto items-center">
            <input type="hidden" name="pipeline_id" value="{{ $pipeline->id }}">
            <select name="assigned_to" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none">
                <option value="">Todos os comerciais</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </form>

        <a href="{{ route('leads.create', ['pipeline_id' => $pipeline->id]) }}" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-indigo-700">+ Novo Lead</a>
    </div>

    {{-- Kanban board --}}
    <div class="flex gap-4 overflow-x-auto pb-4" id="kanban-board">
        @foreach($pipeline->stages as $stage)
        <div class="kanban-column flex-shrink-0 w-72" data-stage-id="{{ $stage->id }}">
            {{-- Column header --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $stage->color }}"></div>
                    <span class="text-sm font-semibold text-gray-700">{{ $stage->name }}</span>
                    <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">{{ $stage->leads->count() }}</span>
                </div>
                @php $stageTotal = $stage->leads->whereNotNull('value')->sum('value'); @endphp
                @if($stageTotal > 0)
                <span class="text-xs text-gray-400">{{ number_format($stageTotal, 0, ',', '.') }} €</span>
                @endif
            </div>

            {{-- Cards container --}}
            <div class="lead-cards space-y-2 min-h-24 bg-gray-50 rounded-xl p-2" data-stage-id="{{ $stage->id }}">
                @foreach($stage->leads as $lead)
                <div class="lead-card bg-white rounded-lg border border-gray-200 p-3 shadow-sm cursor-grab hover:shadow-md transition"
                     data-lead-id="{{ $lead->id }}" draggable="true">
                    <div class="flex items-start justify-between">
                        <a href="{{ route('leads.show', $lead) }}" class="text-sm font-medium text-gray-800 hover:text-indigo-600 leading-tight">{{ $lead->name }}</a>
                        <a href="{{ route('leads.edit', $lead) }}" class="text-gray-300 hover:text-gray-500 ml-1 shrink-0">✎</a>
                    </div>
                    @if($lead->company)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $lead->company }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-2">
                        @if($lead->value)
                        <span class="text-xs font-semibold text-green-600">{{ number_format($lead->value, 0, ',', '.') }} €</span>
                        @else
                        <span></span>
                        @endif
                        <div class="flex gap-1">
                            @if($lead->source)
                            <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $lead->source }}</span>
                            @endif
                            <a href="{{ route('proposals.create', ['lead_id' => $lead->id]) }}"
                               class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-1.5 py-0.5 rounded">+ Proposta</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const containers = document.querySelectorAll('.lead-cards');
        containers.forEach(container => {
            new Sortable(container, {
                group: 'leads',
                animation: 150,
                ghostClass: 'opacity-40',
                onEnd: function (evt) {
                    const leadId  = evt.item.dataset.leadId;
                    const stageId = evt.to.dataset.stageId;
                    const order   = [...evt.to.children].indexOf(evt.item);

                    fetch(`/leads/${leadId}/move`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ pipeline_stage_id: parseInt(stageId), order })
                    });
                }
            });
        });
    });
    </script>
</x-app-layout>
