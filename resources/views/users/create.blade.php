<x-app-layout>
    <x-slot name="title">Novo Utilizador</x-slot>

    <div class="max-w-lg">
        <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Utilizadores</a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
            <h2 class="text-lg font-semibold mb-5">Novo Utilizador</h2>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                @include('users._form')
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Criar Utilizador</button>
                    <a href="{{ route('users.index') }}" class="px-5 py-2 rounded-lg text-sm bg-gray-100 hover:bg-gray-200">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
