<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta Aceite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center max-w-md">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Proposta Aceite!</h1>
        <p class="text-gray-500">Obrigado por aceitar a proposta <strong>{{ $proposal->title }}</strong>.</p>
        <p class="text-gray-500 mt-2">A equipa {{ \App\Models\Setting::get('company_name', 'Webhs') }} irá entrar em contacto brevemente.</p>
    </div>
</body>
</html>
