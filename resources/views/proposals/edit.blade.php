<x-app-layout>
    <x-slot name="title">Editar Proposta</x-slot>

    <div class="max-w-3xl">
        <a href="{{ route('proposals.show', $proposal) }}" class="text-sm text-gray-500 hover:text-indigo-600">← Proposta</a>

        @php $existingItems = is_array($proposal->items) ? $proposal->items : []; @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4"
             x-data="{
                items: {{ json_encode(count($existingItems) ? $existingItems : [['description'=>'','qty'=>1,'unit_price'=>0,'total'=>0]]) }},
                discount: {{ $proposal->discount ?? 0 }},
                get subtotal() { return this.items.reduce((s, i) => s + (parseFloat(i.qty)||0) * (parseFloat(i.unit_price)||0), 0) },
                get total() { return this.subtotal - (parseFloat(this.discount)||0) },
                addItem() { this.items.push({ description: '', qty: 1, unit_price: 0, total: 0 }) },
                removeItem(idx) { if(this.items.length > 1) this.items.splice(idx, 1) },
                formatEur(v) { return new Intl.NumberFormat('pt-PT', {minimumFractionDigits:2,maximumFractionDigits:2}).format(v) + ' €' }
             }">
            <h2 class="text-lg font-semibold mb-5">Editar Proposta</h2>

            <form method="POST" action="{{ route('proposals.update', $proposal) }}">
                @csrf @method('PUT')
                @include('proposals._form')
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                    <a href="{{ route('proposals.show', $proposal) }}" class="px-5 py-2 rounded-lg text-sm bg-gray-100 hover:bg-gray-200">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
