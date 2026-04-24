<x-app-layout>
    <x-slot name="title">Editar Contacto</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('clients.show', $contact->client_id) }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-4 inline-block">← Cliente</a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('contacts.update', $contact) }}" class="space-y-4">
                @csrf @method('PUT')
                @foreach(['name'=>'Nome','email'=>'Email','phone'=>'Telefone','role'=>'Cargo'] as $field => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ old($field, $contact->$field) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @endforeach
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $contact->notes) }}</textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                    <a href="{{ route('clients.show', $contact->client_id) }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
