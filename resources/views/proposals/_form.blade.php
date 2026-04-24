@php $proposal = $proposal ?? null; @endphp
<div class="space-y-5">
    {{-- Title --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
        <input type="text" name="title" value="{{ old('title', $proposal?->title) }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    {{-- Client / Lead --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <select name="client_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Seleccionar...</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ old('client_id', $proposal?->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lead</label>
            <select name="lead_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Nenhum</option>
                @foreach($leads as $lead)
                <option value="{{ $lead->id }}" {{ old('lead_id', $proposal?->lead_id ?? $selectedLead?->id) == $lead->id ? 'selected' : '' }}>
                    {{ $lead->name }} @if($lead->pipeline)({{ $lead->pipeline->name }})@endif
                </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Preencher a partir de plano --}}
    @if(isset($plans) && $plans->count())
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pré-preencher a partir de plano</label>
        <select x-on:change="
            const opt = $event.target.options[$event.target.selectedIndex];
            if(opt.value) {
                items = [{ description: opt.dataset.name, qty: 1, unit_price: parseFloat(opt.dataset.price)||0, total: parseFloat(opt.dataset.price)||0 }];
            }"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">— Opcional —</option>
            @foreach($plans as $plan)
            <option value="{{ $plan->id }}" data-name="{{ $plan->name }}" data-price="{{ $plan->price }}">{{ $plan->name }} — {{ number_format($plan->price, 2, ',', '.') }} €</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Line items --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Itens</label>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left w-1/2">Descrição</th>
                        <th class="px-3 py-2 text-right w-20">Qtd</th>
                        <th class="px-3 py-2 text-right w-28">Preço unit.</th>
                        <th class="px-3 py-2 text-right w-24">Total</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, idx) in items" :key="idx">
                        <tr class="border-t border-gray-100">
                            <td class="px-2 py-1.5">
                                <input type="text" :name="`items[${idx}][description]`" x-model="item.description" placeholder="Descrição..."
                                       class="w-full border-0 bg-transparent focus:outline-none text-sm px-1">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="number" :name="`items[${idx}][qty]`" x-model.number="item.qty" step="1" min="1"
                                       class="w-full border-0 bg-transparent focus:outline-none text-sm text-right px-1">
                            </td>
                            <td class="px-2 py-1.5">
                                <input type="number" :name="`items[${idx}][unit_price]`" x-model.number="item.unit_price" step="0.01" min="0"
                                       class="w-full border-0 bg-transparent focus:outline-none text-sm text-right px-1">
                            </td>
                            <td class="px-3 py-1.5 text-right font-medium" x-text="formatEur((item.qty||0)*(item.unit_price||0))"></td>
                            <td class="px-2 py-1.5 text-center">
                                <button type="button" @click="removeItem(idx)" class="text-gray-300 hover:text-red-500 text-lg leading-none">×</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="px-3 py-2 border-t border-gray-100">
                <button type="button" @click="addItem()" class="text-sm text-indigo-600 hover:text-indigo-800">+ Adicionar linha</button>
            </div>
        </div>
    </div>

    {{-- Totals --}}
    <div class="flex justify-end">
        <div class="w-64 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span x-text="formatEur(subtotal)"></span>
            </div>
            <div class="flex justify-between items-center text-gray-600">
                <label>Desconto (€)</label>
                <input type="number" name="discount" x-model.number="discount" step="0.01" min="0"
                       class="w-28 border border-gray-300 rounded px-2 py-1 text-right text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-200">
                <span>Total</span>
                <span x-text="formatEur(total)"></span>
            </div>
        </div>
    </div>

    {{-- Expiry & options --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Válida até</label>
            <input type="date" name="expires_at" value="{{ old('expires_at', $proposal?->expires_at?->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-end pb-2">
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="tracking_enabled" value="1"
                       {{ old('tracking_enabled', $proposal?->tracking_enabled ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-indigo-600">
                Activar tracking de visualizações
            </label>
        </div>
    </div>

    {{-- Notes --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notas internas</label>
        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $proposal?->notes) }}</textarea>
    </div>
</div>
