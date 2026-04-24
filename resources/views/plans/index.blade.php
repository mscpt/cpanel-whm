<x-app-layout>
    <x-slot name="title">Planos</x-slot>
    <div class="flex justify-end mb-6">
        <a href="{{ route('plans.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ Novo Plano</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($plans as $plan)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-semibold text-gray-800">{{ $plan->name }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $plan->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $plan->status === 'active' ? 'Activo' : 'Inactivo' }}</span>
            </div>
            @if($plan->description)<p class="text-sm text-gray-500 mb-3">{{ $plan->description }}</p>@endif
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($plan->price, 2, ',', '.') }} €<span class="text-sm font-normal text-gray-400">/{{ ['monthly'=>'mês','yearly'=>'ano','custom'=>''][$plan->billing_cycle] ?? $plan->billing_cycle }}</span></p>
            <p class="text-xs text-gray-400 mt-2">{{ $plan->contracts_count }} contratos</p>
            <div class="flex gap-2 mt-4">
                <a href="{{ route('plans.edit', $plan) }}" class="text-sm text-indigo-600 hover:underline">Editar</a>
                <form method="POST" action="{{ route('plans.destroy', $plan) }}" onsubmit="return confirm('Eliminar plano?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-400 hover:text-red-600">Eliminar</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-400">Nenhum plano criado ainda.</div>
        @endforelse
    </div>
</x-app-layout>
