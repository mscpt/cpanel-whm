<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CRM' }} — {{ \App\Models\Setting::get('company_name', 'Webhs') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    @if(\App\Models\Setting::get('gtm_container_id'))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ \App\Models\Setting::get("gtm_container_id") }}');</script>
    @endif

    @if(\App\Models\Setting::get('ga_measurement_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('ga_measurement_id') }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ \App\Models\Setting::get("ga_measurement_id") }}');</script>
    @endif

    @if(\App\Models\Setting::get('meta_pixel_id'))
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ \App\Models\Setting::get("meta_pixel_id") }}');fbq('track','PageView');</script>
    @endif

    {!! \App\Models\Setting::get('custom_head_scripts') !!}
</head>
<body class="bg-gray-50 text-gray-900">

@if(\App\Models\Setting::get('gtm_container_id'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ \App\Models\Setting::get('gtm_container_id') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col shrink-0">
        <div class="p-4 border-b border-gray-700">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-white">
                {{ \App\Models\Setting::get('company_name', 'Webhs') }} CRM
            </a>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @php
                $navItems = [
                    ['route' => 'dashboard',         'icon' => '⊞', 'label' => 'Dashboard'],
                    ['route' => 'clients.index',     'icon' => '👥', 'label' => 'Clientes'],
                    ['route' => 'leads.index',       'icon' => '⬡', 'label' => 'Pipeline / Leads'],
                    ['route' => 'proposals.index',   'icon' => '📄', 'label' => 'Propostas'],
                    ['route' => 'contracts.index',   'icon' => '📋', 'label' => 'Contratos'],
                    ['route' => 'plans.index',       'icon' => '💠', 'label' => 'Planos'],
                    ['route' => 'import.index',      'icon' => '⬆', 'label' => 'Importar CSV'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition
                          {{ request()->routeIs(explode('.', $item['route'])[0].'*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            @if(auth()->user()->isAdmin())
            <div class="pt-4 border-t border-gray-700 mt-4 space-y-1">
                <p class="text-xs text-gray-500 uppercase tracking-wider px-3 pb-1">Admin</p>
                <a href="{{ route('pipelines.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <span>⚙</span><span>Pipelines</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <span>👤</span><span>Utilizadores</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <span>🔧</span><span>Definições</span>
                </a>
                <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <span>🔍</span><span>Auditoria</span>
                </a>
            </div>
            @endif
        </nav>
        <div class="p-4 border-t border-gray-700 text-sm text-gray-400">
            <p class="font-medium text-white">{{ auth()->user()->name }}</p>
            <p class="text-xs capitalize">{{ auth()->user()->role }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white text-xs">Sair →</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
            <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'CRM' }}</h1>
            <div class="flex items-center gap-3">
                @if(session('success'))
                    <span x-data x-init="setTimeout(()=>$el.remove(),4000)" class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">{{ session('success') }}</span>
                @endif
                @if($errors->any())
                    <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full">{{ $errors->first() }}</span>
                @endif
            </div>
        </header>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </div>
    </main>
</div>

{!! \App\Models\Setting::get('custom_body_scripts') !!}
</body>
</html>
