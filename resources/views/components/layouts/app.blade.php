<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tituloPagina ?? 'Cadastro de Usuários' }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{--class="bg-gray-100 min-h-screen"--}}
<body class="bg-gray-50 dark:bg-gray-900">
<div class="container mx-auto px-4 py-4">
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-center text-gray-400">{{ $tituloFormulario ?? 'REALIZE SEU CADASTRO AQUI'}}</h1>
    </header>

    <main>
        {{ $slot }}
    </main>

{{--    <footer class="mt-8 text-right text-gray-500">--}}
{{--        <p>&copy; {{ date('Y') }} LivewireTeste. Todos os direitos reservados.</p>--}}
{{--    </footer>--}}
</div>

@livewireScripts
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
