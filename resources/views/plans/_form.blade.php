@php $plan = $plan ?? null; @endphp
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
    <input type="text" name="name" value="{{ old('name', $plan?->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $plan?->description) }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Preço (€) *</label>
        <input type="number" name="price" value="{{ old('price', $plan?->price) }}" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Faturação *</label>
        <select name="billing_cycle" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="monthly" {{ old('billing_cycle', $plan?->billing_cycle) === 'monthly' ? 'selected' : '' }}>Mensal</option>
            <option value="yearly"  {{ old('billing_cycle', $plan?->billing_cycle) === 'yearly'  ? 'selected' : '' }}>Anual</option>
            <option value="custom"  {{ old('billing_cycle', $plan?->billing_cycle) === 'custom'  ? 'selected' : '' }}>Personalizado</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="active"   {{ old('status', $plan?->status) !== 'inactive' ? 'selected' : '' }}>Activo</option>
            <option value="inactive" {{ old('status', $plan?->status) === 'inactive' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
</div>
