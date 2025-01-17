<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME', 'Cadastro Geral') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<div class="container mx-auto px-4 py-4">
    <!-- Barra de Navegação -->
    <header class="flex justify-between items-center py-4">
        <div class="text-white text-2xl font-bold">
            LOGO
        </div>
        <nav>
            @if (Route::has('filament.adm.auth.login'))
                @auth
                    <a href="{{ '/' }}" class="text-white text-lg">Painel</a>
                @else
                    <a href="{{ route('filament.adm.auth.login') }}" class="text-white text-lg">Login</a>
                    <a href="{{ route('register') }}" class="ml-4 text-white text-lg">Registrar</a>
                @endauth
            @endif
        </nav>
    </header>

    <!-- Banner com Imagem de Fundo -->
    <div class="relative bg-cover bg-center h-64" style="background-image: url('images/jardim-das-rosas.jpg');">
        <div class="absolute inset-0 bg-blue-900 bg-opacity-60 flex items-center justify-center">
            <h1 class="text-white text-4xl font-bold">Bem-vindo(a) ao Sistema</h1>
        </div>
    </div>

    <!-- Slides de Imagens -->
    <div class="mt-8">
        <div x-data="{ slide: 0 }" class="relative">
            <div x-show="slide === 0" class="h-48 bg-blue-700 flex items-center justify-center text-white">
                <h2 class="text-2xl">Notícia ou Serviço 1</h2>
            </div>
            <div x-show="slide === 1" class="h-48 bg-blue-600 flex items-center justify-center text-white">
                <h2 class="text-2xl">Notícia ou Serviço 2</h2>
            </div>
            <div x-show="slide === 2" class="h-48 bg-blue-500 flex items-center justify-center text-white">
                <h2 class="text-2xl">Notícia ou Serviço 3</h2>
            </div>
            <button @click="slide = (slide + 1) % 3" class="absolute top-1/2 right-0 transform -translate-y-1/2 bg-white px-4 py-2">Próximo</button>
        </div>
    </div>

    <!-- Cards de Serviços -->
    <div class="mt-8">
        <!-- Primeira linha de cards -->
        <div class="flex flex-col md:flex-row gap-4 mb-4">
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 1</h3>
                <p class="text-gray-600">Descrição breve do serviço 1.</p>
            </div>
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 2</h3>
                <p class="text-gray-600">Descrição breve do serviço 2.</p>
            </div>
        </div>

        <!-- Segunda linha de cards -->
        <div class="flex flex-col md:flex-row gap-4 mb-4">
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 3</h3>
                <p class="text-gray-600">Descrição breve do serviço 3.</p>
            </div>
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 4</h3>
                <p class="text-gray-600">Descrição breve do serviço 4.</p>
            </div>
        </div>

        <!-- Terceira linha de cards -->
        <div class="flex flex-col md:flex-row gap-4 mb-4">
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 5</h3>
                <p class="text-gray-600">Descrição breve do serviço 5.</p>
            </div>
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 6</h3>
                <p class="text-gray-600">Descrição breve do serviço 6.</p>
            </div>
        </div>

        <!-- Quarta linha de cards -->
        <div class="flex flex-col md:flex-row gap-4">
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 7</h3>
                <p class="text-gray-600">Descrição breve do serviço 7.</p>
            </div>
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h3 class="text-xl font-semibold">Serviço 8</h3>
                <p class="text-gray-600">Descrição breve do serviço 8.</p>
            </div>
        </div>
    </div>

    <!-- Notícia Destaque -->
    <div class="mt-8">
        <!-- Container para notícia e tabela -->
        <div class="flex flex-col md:flex-row gap-4 mb-4">
            <!-- Card de Notícia em Destaque -->
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h2 class="text-2xl font-bold mb-4 text-blue-800">Notícia em Destaque</h2>
                <img src="/images/noticia-destaque.jpg" alt="Imagem da Notícia" class="w-full h-48 object-cover mb-4 rounded">
                <h3 class="text-xl font-semibold mb-2">Título da Notícia Importante</h3>
                <p class="text-gray-600 mb-4">Breve descrição da notícia em destaque. Este espaço pode conter um resumo conciso do conteúdo principal.</p>
                <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">Leia mais →</a>
            </div>

            <!-- Tabela de Aniversariantes -->
            <div class="bg-white p-4 rounded shadow hover:shadow-lg flex-1">
                <h2 class="text-2xl font-bold mb-4 text-blue-800">Aniversariantes do Mês</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr class="bg-blue-700 text-white">
                            <th class="p-3 text-left">Dia</th>
                            <th class="p-3 text-left">Nome</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="border-b">
                            <td class="p-3">17</td>
                            <td class="p-3 flex items-center">
                                <span class="text-xl mr-2">🎂</span>
                                João Silva
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-3">20</td>
                            <td class="p-3">Maria Souza</td>
                        </tr>
                        <tr>
                            <td class="p-3">25</td>
                            <td class="p-3">Carlos Oliveira</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="mt-8 text-center text-gray-500">
        <p>&copy; {{ date('Y') }} Emerson Ferreira. Todos os direitos reservados.</p>
    </footer>
</div>

@livewireScripts
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
