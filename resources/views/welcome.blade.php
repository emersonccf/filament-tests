<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Cadastro Geral') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gradient-to-r from-[#006eb6] to-[#002e98] min-h-screen">

<div class="container mx-auto px-4 py-8">
    <!-- Barra de Navegação -->
    <nav class="bg-white bg-opacity-10 rounded-lg shadow-lg mb-8" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 text-cyan-50">
                        <img class="h-8 w-auto" src="/path/to/your/logo.png" alt="LOGOSYS">
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        @auth
                            <a href="{{ '/' }}"  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium">Painel de <span style="color: yellow;">{{ auth()->user()->name }}</span></a>

                            <a href="#"  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Logoff</a>

                            <form id="logout-form" action="{{ route('filament.adm.auth.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>


                        @else
                            <a href="{{ route('filament.adm.auth.login') }}" class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="{{ route('register') }}" class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium">Cadastre-se</a>
                        @endauth
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                        <span class="sr-only">Abrir menu principal</span>
                        <svg class="h-6 w-6" x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="open" class="md:hidden" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                @auth
                    <a href="{{ '/' }}"  class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-base font-medium">Painel de <span style="color: yellow;">{{ auth()->user()->name }}</span></a>

                    <a href="#"  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Logoff</a>

                    <form id="logout-form" action="{{ route('filament.adm.auth.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('filament.adm.auth.login') }}" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-base font-medium">Login</a>
                    <a href="{{ route('register') }}" class="text-white hover:bg-white hover:bg-opacity-20 block px-3 py-2 rounded-md text-base font-medium">Cadastre-se</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Coluna da Esquerda -->
        <div class="col-span-1">
            <!-- Acesso Rápido aos Sistemas -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-[#006eb6] mb-4">Acesso Rápido</h2>
                <div class="space-y-4">
                    <a href="#" class="block bg-[#006eb6] text-white p-3 rounded-lg hover:bg-[#002e98] transition duration-300">
                        Recadastramento Anual
                    </a>
                    <a href="#" class="block bg-[#006eb6] text-white p-3 rounded-lg hover:bg-[#002e98] transition duration-300">
                        Fardamento
                    </a>
                    <a href="#" class="block bg-[#006eb6] text-white p-3 rounded-lg hover:bg-[#002e98] transition duration-300">
                        E-Protocolo
                    </a>
                    <a href="#" class="block bg-[#006eb6] text-white p-3 rounded-lg hover:bg-[#002e98] transition duration-300">
                        Acesso Seu Painel
                    </a>
                    <!-- Adicione mais links conforme necessário -->
                </div>
            </div>

            <!-- Frase para reflexão -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-[#006eb6] mb-4">Frase para reflexão</h2>
                <p id="quote" class="italic text-gray-600">
                    <!-- Aqui será inserida a frase aleatória via JavaScript -->
                </p>
            </div>

            <!-- Jornal Transalvador (movido para baixo) -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-bold text-[#006eb6] mb-4">Jornal Interno</h2>
                <a href="#" class="block text-center">
                    <img src="/path/to/jornal-image.jpg" alt="Jornal Interno" class="w-full h-auto rounded-lg">
                </a>
            </div>

            <!-- Espaço vago (pode ser usado para futuras adições) -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <!-- Este espaço está intencionalmente vazio -->
            </div>

        </div>

        <!-- Coluna Central -->
        <div class="col-span-1 md:col-span-2">
            <!-- Carrossel de Notícias -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8" x-data="{ activeSlide: 0 }">
                <h2 class="text-2xl font-bold text-[#006eb6] mb-4">Notícias</h2>
                <div class="relative">
                    <div class="overflow-hidden rounded-lg">
                        <div class="flex transition-transform duration-300 ease-in-out" :style="{ transform: `translateX(-${activeSlide * 100}%)` }">
                            <!-- Slide 1 -->
                            <div class="w-full flex-shrink-0">
                                <img src="/path/to/news1.jpg" alt="Notícia 1" class="w-full h-64 object-cover rounded-lg">
                                <h3 class="mt-4 text-xl font-semibold">Título da Notícia 1</h3>
                                <p class="mt-2 text-gray-600">Breve descrição da notícia 1...</p>
                            </div>
                            <!-- Slide 2 -->
                            <div class="w-full flex-shrink-0">
                                <img src="/path/to/news2.jpg" alt="Notícia 2" class="w-full h-64 object-cover rounded-lg">
                                <h3 class="mt-4 text-xl font-semibold">Título da Notícia 2</h3>
                                <p class="mt-2 text-gray-600">Breve descrição da notícia 2...</p>
                            </div>
                            <!-- Adicione mais slides conforme necessário -->
                        </div>
                    </div>
                    <button @click="activeSlide = (activeSlide - 1 + 3) % 3" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full">
                        &#10094;
                    </button>
                    <button @click="activeSlide = (activeSlide + 1) % 3" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full">
                        &#10095;
                    </button>
                </div>
            </div>

            <!-- Links Úteis e Aniversariantes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Links Úteis -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-[#006eb6] mb-4">Links Úteis</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="bg-[#006eb6] text-white p-3 rounded-lg text-center hover:bg-[#002e98] transition duration-300">
                            Diário Oficial
                        </a>
                        <a href="#" class="bg-[#006eb6] text-white p-3 rounded-lg text-center hover:bg-[#002e98] transition duration-300">
                            Contra Cheque
                        </a>
                        <a href="#" class="bg-[#006eb6] text-white p-3 rounded-lg text-center hover:bg-[#002e98] transition duration-300">
                            Tabela de Turnos
                        </a>
                        <a href="#" class="bg-[#006eb6] text-white p-3 rounded-lg text-center hover:bg-[#002e98] transition duration-300">
                            Telefone Úteis
                        </a>
                    </div>
                </div>

                <!-- Aniversariantes do Mês -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-[#006eb6] mb-4">Aniversariantes de <span id="currentMonth"></span></h2>
                    <ul class="space-y-2" id="birthdayList">
                        <!-- A lista de aniversariantes será preenchida via JavaScript -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="mt-12 text-center text-white">
        <p>&copy; {{ date('Y') }} Emerson Ferreira. Todos os direitos reservados.</p>
    </footer>
</div>
@livewireScripts
<script src="{{ asset('js/app.js') }}"></script>
<script>
    // ... (mantenha o código anterior para as frases de reflexão) ...

    // Função para obter o nome do mês atual
    function getCurrentMonth() {
        const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        const currentDate = new Date();
        return months[currentDate.getMonth()];
    }

    // Função para verificar se é o aniversário da pessoa hoje
    function isBirthdayToday(day) {
        const currentDate = new Date();
        return currentDate.getDate() === day;
    }

    // Lista de aniversariantes (dia, nome)
    const birthdays = [
        { day: 5, name: "João da Silva" },
        { day: 17, name: "Maria Souza" },
        { day: 18, name: "Pedro Oliveira" },
        { day: 25, name: "Ana Santos" },
        { day: 30, name: "Carlos Ferreira" }
    ];

    // Função para preencher a lista de aniversariantes
    function fillBirthdayList() {
        const birthdayList = document.getElementById('birthdayList');
        birthdays.sort((a, b) => a.day - b.day); // Ordena por dia

        birthdays.forEach(person => {
            const listItem = document.createElement('li');
            const birthdayCake = isBirthdayToday(person.day) ? "🎂 " : "";
            listItem.textContent = `${birthdayCake}${person.day} - ${person.name}`;
            birthdayList.appendChild(listItem);
        });
    }

    // Preencher o mês atual e a lista de aniversariantes
    document.getElementById('currentMonth').textContent = getCurrentMonth();
    fillBirthdayList();
</script>

<script>
    // Array de frases para reflexão
    const quotes = [
        "A persistência é o caminho do êxito.",
        "O sucesso nasce do querer, da determinação e persistência em se chegar a um objetivo.",
        "Obstáculo é o que você enxerga quando tira o olhar de seus objetivos.",
        "Não espere por uma crise para descobrir o que é importante em sua vida.",
        "A maior glória não é ficar de pé, mas levantar-se cada vez que se cai.",
        "O insucesso é apenas uma oportunidade para recomeçar com mais inteligência."
    ];

    // Função para selecionar uma frase aleatória
    function getRandomQuote() {
        return quotes[Math.floor(Math.random() * quotes.length)];
    }

    // Inserir a frase aleatória no elemento HTML
    document.getElementById('quote').textContent = getRandomQuote();
</script>

</body>
</html>
