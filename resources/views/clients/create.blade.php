<x-app-layout>
    <x-slot name="title">Novo Cliente</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('clients.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-4 inline-block">← Clientes</a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('clients.store') }}" class="space-y-4">
                @csrf
                @include('clients._form')
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Criar Cliente</button>
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
