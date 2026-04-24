<x-app-layout>
    <x-slot name="title">Editar Plano</x-slot>
    <div class="max-w-lg">
        <a href="{{ route('plans.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 mb-4 inline-block">← Planos</a>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('plans.update', $plan) }}" class="space-y-4">
                @csrf @method('PUT')
                @include('plans._form')
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                    <a href="{{ route('plans.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
