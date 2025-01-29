<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tituloPagina ?? 'Cadastro de Usuários' }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        #notification {
            animation: fadeOut 1s ease-in-out 9s forwards;
        }

        #notification.shake {
            animation: shake 0.2s ease-in-out;
        }
    </style>
</head>
{{--class="bg-gray-100 min-h-screen"--}}
<body class="bg-gray-50 dark:bg-gray-900">
{{-- exibir mensagens enviadas para a home--}}
@if (session('message'))
    <div id="notification"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => { show = false }, 10000); setInterval(() => { $el.classList.toggle('shake') }, 100)"
         class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-md shadow-lg z-50 transition-opacity duration-1000"
         :class="{ 'opacity-0': !show }"
    >
        <p>{{ session('message') }}</p>
    </div>
@endif

<x-flash-message /> {{-- component flash-card --}}
<div> <!-- class="container mx-auto px-4 py-4" -->
    @if( $tituloFormulario )
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-center text-gray-400">{{ $tituloFormulario }}</h1>
    </header>
    @endif

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
