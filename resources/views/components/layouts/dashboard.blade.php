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

</head>
<body class="antialiased md:flex dark:bg-slate-900 bg-white text-slate-500 dark:text-slate-400" x-data="sidebar">
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
