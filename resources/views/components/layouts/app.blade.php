<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Cadastro de Usuários' }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
<div class="container mx-auto px-4 py-8">
    <header class="mb-8">
        <h1 style="padding-top: 5px; font-size: 30px" class="text-3xl font-bold text-center text-gray-800">REALIZE SEU CADASTRO AQUI</h1>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-8 text-center text-gray-600">
        <p>&copy; {{ date('Y') }} LivewireTeste. Todos os direitos reservados.</p>
    </footer>
</div>

@livewireScripts
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
