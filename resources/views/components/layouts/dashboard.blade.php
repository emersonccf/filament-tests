<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="darkmode" :class="{'dark': darkmodeOn}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SysCAD - @yield('title', 'Dashboard')</title>
    <!-- Estilos específicos do Dashboard -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
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
<body class="antialiased md:flex dark:bg-slate-900 bg-white text-slate-500 dark:text-slate-400" x-data="sidebar">
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
    <x-dashboard.sidebar />

    <main class="flex flex-col md:flex md:flex-1 md:h-full md:ml-64" x-ref="main">
        <x-dashboard.header />

        <div class="w-full p-2 mt-14 md:p-4">
            @yield('content')
        </div>
    </main>

@livewireScripts
@stack('scripts')
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
