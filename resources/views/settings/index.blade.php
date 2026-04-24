<x-app-layout>
    <x-slot name="title">Definições</x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf @method('PUT')

            {{-- Company --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-base font-semibold text-gray-800 mb-5">Informação da Empresa</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da empresa</label>
                        <input type="text" name="company_name" value="{{ $settings['company_name'] }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="company_email" value="{{ $settings['company_email'] }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="company_phone" value="{{ $settings['company_phone'] }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Morada</label>
                        <input type="text" name="company_address" value="{{ $settings['company_address'] }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL do Logótipo</label>
                        <input type="url" name="company_logo_url" value="{{ $settings['company_logo_url'] }}" placeholder="https://..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            {{-- Pixel IDs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-base font-semibold text-gray-800 mb-1">Pixel & Analytics de Terceiros</h2>
                <p class="text-xs text-gray-400 mb-5">Estes códigos são injectados automaticamente em todas as páginas do CRM e na área de cliente.</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Pixel ID</label>
                        <input type="text" name="meta_pixel_id" value="{{ $settings['meta_pixel_id'] }}" placeholder="1234567890"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics (Measurement ID)</label>
                        <input type="text" name="ga_measurement_id" value="{{ $settings['ga_measurement_id'] }}" placeholder="G-XXXXXXXXXX"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Tag Manager (Container ID)</label>
                        <input type="text" name="gtm_container_id" value="{{ $settings['gtm_container_id'] }}" placeholder="GTM-XXXXXXX"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            {{-- Custom scripts --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-base font-semibold text-gray-800 mb-1">Scripts / Pixel Próprio</h2>
                <p class="text-xs text-gray-400 mb-5">Cole aqui qualquer código HTML/JavaScript adicional. O código do &lt;head&gt; é inserido antes de &lt;/head&gt;, o do &lt;body&gt; antes de &lt;/body&gt;.</p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scripts no &lt;head&gt;</label>
                        <textarea name="custom_head_scripts" rows="4" placeholder="<!-- Cole aqui o seu código de tracking, pixel, etc. -->"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['custom_head_scripts'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scripts antes de &lt;/body&gt;</label>
                        <textarea name="custom_body_scripts" rows="4" placeholder="<!-- Cole aqui scripts a carregar no final da página -->"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $settings['custom_body_scripts'] }}</textarea>
                    </div>
                </div>

                {{-- Pixel próprio stats --}}
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">📊 Pixel Próprio (webhs.pt)</h3>
                    <p class="text-xs text-gray-500 mb-3">
                        URL do pixel: <code class="bg-gray-100 px-1 rounded">{{ url('/track/{token}.gif') }}</code><br>
                        Use um token único por contexto (proposta, email, campanha) para segmentar os eventos.
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([['Total de eventos', $trackStats['total']],['Hoje', $trackStats['today']],['Esta semana', $trackStats['this_week']]] as [$label, $value])
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-xl font-bold text-indigo-600">{{ $value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-indigo-700">
                Guardar Definições
            </button>
        </form>
    </div>
</x-app-layout>
