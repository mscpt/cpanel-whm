<x-app-layout>
    <x-slot name="title">Editar Lead</x-slot>
    <div class="max-w-xl">
        <a href="{{ route('leads.show', $lead) }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-4 inline-block">← {{ $lead->name }}</a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                        <input type="text" name="name" value="{{ old('name', $lead->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <input type="text" name="company" value="{{ old('company', $lead->company) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $lead->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor estimado (€)</label>
                        <input type="number" name="value" value="{{ old('value', $lead->value) }}" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pipeline *</label>
                        <select name="pipeline_id" id="pipeline_select" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($pipelines as $p)
                            <option value="{{ $p->id }}" {{ $lead->pipeline_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etapa *</label>
                        <select name="pipeline_stage_id" id="stage_select" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($pipelines->firstWhere('id', $lead->pipeline_id)?->stages ?? [] as $stage)
                            <option value="{{ $stage->id }}" {{ $lead->pipeline_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Origem</label>
                        <input type="text" name="source" value="{{ old('source', $lead->source) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
                        <select name="assigned_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">—</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $lead->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $lead->notes) }}</textarea>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                    <a href="{{ route('leads.show', $lead) }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <script>
    @php $stagesMap = $pipelines->mapWithKeys(fn($p) => [$p->id => $p->stages->map(fn($s) => ['id'=>$s->id,'name'=>$s->name,'selected'=>$s->id==$lead->pipeline_stage_id])])->toJson(); @endphp
    const stagesMap = {!! $stagesMap !!};
    document.getElementById('pipeline_select').addEventListener('change', function() {
        const stages = stagesMap[this.value] || [];
        const sel = document.getElementById('stage_select');
        sel.innerHTML = stages.map(s => `<option value="${s.id}" ${s.selected?'selected':''}>${s.name}</option>`).join('');
    });
    </script>
</x-app-layout>
