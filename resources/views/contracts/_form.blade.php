@php $contract = $contract ?? null; @endphp
<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
        <select name="client_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Seleccionar cliente...</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ old('client_id', $contract?->client_id ?? $selectedClient?->id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Plano</label>
        <select name="plan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Sem plano</option>
            @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ old('plan_id', $contract?->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
        <input type="text" name="title" value="{{ old('title', $contract?->title) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Valor (€) *</label>
        <input type="number" name="value" value="{{ old('value', $contract?->value) }}" step="0.01" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Faturação</label>
        <select name="billing_cycle" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="monthly" {{ old('billing_cycle', $contract?->billing_cycle) === 'monthly' ? 'selected' : '' }}>Mensal</option>
            <option value="yearly"  {{ old('billing_cycle', $contract?->billing_cycle) === 'yearly'  ? 'selected' : '' }}>Anual</option>
            <option value="custom"  {{ old('billing_cycle', $contract?->billing_cycle) === 'custom'  ? 'selected' : '' }}>Personalizado</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data de início *</label>
        <input type="date" name="start_date" value="{{ old('start_date', $contract?->start_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data de renovação</label>
        <input type="date" name="renewal_date" value="{{ old('renewal_date', $contract?->renewal_date?->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="active"    {{ old('status', $contract?->status ?? 'active') === 'active'    ? 'selected' : '' }}>Activo</option>
            <option value="suspended" {{ old('status', $contract?->status) === 'suspended' ? 'selected' : '' }}>Suspenso</option>
            <option value="cancelled" {{ old('status', $contract?->status) === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $contract?->notes) }}</textarea>
    </div>
</div>
