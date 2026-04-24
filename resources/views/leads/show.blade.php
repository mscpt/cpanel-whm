<x-app-layout>
    <x-slot name="title">{{ $lead->name }}</x-slot>

    <div class="flex items-start justify-between mb-6">
        <div>
            <a href="{{ route('leads.index', ['pipeline_id' => $lead->pipeline_id]) }}" class="text-sm text-gray-500 hover:text-indigo-600">← Pipeline</a>
            <h2 class="text-2xl font-bold mt-1">{{ $lead->name }}</h2>
            <div class="flex items-center gap-3 mt-1">
                <span class="inline-flex items-center gap-1 text-sm text-gray-500">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $lead->stage->color }}"></span>
                    {{ $lead->stage->name }}
                </span>
                @if($lead->company)<span class="text-sm text-gray-400">· {{ $lead->company }}</span>@endif
                @if($lead->value)<span class="text-sm font-semibold text-green-600">· {{ number_format($lead->value, 2, ',', '.') }} €</span>@endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('proposals.create', ['lead_id' => $lead->id]) }}" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">+ Proposta</a>
            <a href="{{ route('leads.edit', $lead) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200">Editar</a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <div class="col-span-2 space-y-4">
            {{-- Activity form --}}
            @include('activities._form', ['activityableType' => 'lead', 'activityableId' => $lead->id])

            {{-- Timeline --}}
            <div class="space-y-3 mt-4">
                @forelse($lead->activities as $activity)
                <div class="bg-white rounded-xl border border-gray-100 p-4 flex gap-3">
                    <span class="text-xl">{{ ['note'=>'📝','call'=>'📞','email'=>'✉️','meeting'=>'📅'][$activity->type] ?? '•' }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ $activity->subject }}</p>
                        <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $activity->user->name }} · {{ $activity->occurred_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Sem atividades.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            {{-- Contact info --}}
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Contacto</p>
                @if($lead->email)<p class="text-sm">✉️ {{ $lead->email }}</p>@endif
                @if($lead->phone)<p class="text-sm mt-1">📞 {{ $lead->phone }}</p>@endif
                @if($lead->source)<p class="text-sm mt-1 text-gray-400">Origem: {{ $lead->source }}</p>@endif
                @if($lead->assignedUser)<p class="text-sm mt-1 text-gray-400">Responsável: {{ $lead->assignedUser->name }}</p>@endif
            </div>

            {{-- Proposals --}}
            @if($lead->proposals->count())
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Propostas</p>
                @foreach($lead->proposals as $proposal)
                <div class="flex items-center justify-between py-1">
                    <a href="{{ route('proposals.show', $proposal) }}" class="text-sm text-indigo-600 hover:underline">{{ $proposal->title }}</a>
                    <span class="text-xs px-1.5 py-0.5 rounded-full {{ ['draft'=>'bg-gray-100 text-gray-600','sent'=>'bg-blue-100 text-blue-600','viewed'=>'bg-yellow-100 text-yellow-600','accepted'=>'bg-green-100 text-green-600','rejected'=>'bg-red-100 text-red-600'][$proposal->status] ?? '' }}">
                        {{ ucfirst($proposal->status) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            @if($lead->notes)
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Notas</p>
                <p class="text-sm text-gray-600">{{ $lead->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
