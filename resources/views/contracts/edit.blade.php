<x-app-layout>
    <x-slot name="title">Editar Contrato</x-slot>

    <div class="max-w-2xl">
        <a href="{{ route('contracts.show', $contract) }}" class="text-sm text-gray-500 hover:text-indigo-600">← Contrato</a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
            <h2 class="text-lg font-semibold mb-5">Editar Contrato</h2>
            <form method="POST" action="{{ route('contracts.update', $contract) }}">
                @csrf @method('PUT')
                @include('contracts._form')
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                    <a href="{{ route('contracts.show', $contract) }}" class="px-5 py-2 rounded-lg text-sm bg-gray-100 hover:bg-gray-200">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
