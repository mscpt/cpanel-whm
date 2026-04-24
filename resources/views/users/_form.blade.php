@php $user = $user ?? null; $isEdit = $isEdit ?? false; @endphp
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
        <input type="email" name="email" value="{{ old('email', $user?->email) }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Perfil *</label>
        <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="commercial" {{ old('role', $user?->role) === 'commercial' ? 'selected' : '' }}>Comercial</option>
            <option value="admin" {{ old('role', $user?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    @if($isEdit)
    <div>
        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user?->is_active) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-indigo-600">
            Conta activa
        </label>
    </div>
    @endif
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password {{ $isEdit ? '(deixar em branco para manter)' : '*' }}
        </label>
        <input type="password" name="password" {{ $isEdit ? '' : 'required' }} minlength="12"
               placeholder="Mínimo 12 caracteres"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Password {{ $isEdit ? '' : '*' }}</label>
        <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }} minlength="12"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <p class="text-xs text-gray-400">A password deve ter pelo menos 12 caracteres (requisito de segurança NIS2).</p>
</div>
