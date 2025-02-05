<div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden">

    @include("livewire.dashboard.sidebar")

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-6 transition-colors duration-300">
            <div class="flex items-center">
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    class="text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none transition duration-200 lg:hidden"
                >
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>

                <!-- ALTERAÇÃO: Título movido para o header -->
                <h1 class="text-xl text-slate-800 dark:text-slate-50 hidden lg:block">
                    <i class="fa-solid fa-gauge"></i> Painel de Atividades de
                    <span class="text-blue-700 dark:text-yellow-500">{{ $userName }}</span>
                </h1>

            </div>

{{--            <!-- Espaçador invisível para telas grandes -->--}}
{{--            <div class="hidden lg:block flex-grow"></div>--}}

           <!-- Right side menu -->
            <div class="flex items-center ml-auto">
                <!-- Notifications -->
                <div x-data="{ notificationsOpen: false }" class="relative mr-4">
                    <button
                        @click="notificationsOpen = !notificationsOpen"
                        class="text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none"
                    >
                        <x-heroicon-o-chat-bubble-left class="w-6 h-6" />
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">3</span>
                    </button>

                    <!-- Notifications dropdown -->
                    <div
                        x-show="notificationsOpen"
                        @click.away="notificationsOpen = false"
                        x-cloak
                        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg overflow-hidden z-20"
                    >
                        <div class="py-2">
                            <a href="#" class="flex items-center px-4 py-3 border-b hover:bg-gray-100 dark:hover:bg-gray-700 -mx-2">
                                <img class="h-8 w-8 rounded-full object-cover mx-1" src="{{ Storage::url('images/avatar-2.jpg') }}" alt="avatar">
                                <p class="text-gray-600 dark:text-gray-300 text-sm mx-2">
                                    <span class="font-bold">Sara Salah</span> replied on the <span class="font-bold text-blue-500">Upload Image</span> article. 2m
                                </p>
                            </a>
                            <!-- More notification items -->
                        </div>
                        <a href="#" class="block bg-gray-800 text-white text-center font-bold py-2">See all notifications</a>
                    </div>
                </div>

                <!-- Profile dropdown -->
                <div x-data="{ profileOpen: false }" class="relative">
                    <button
                        @click="profileOpen = !profileOpen"
                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out"
                    >
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url('images/avatar-3.jpg') }}" alt="Sua Foto">
                    </button>

                    <div
                        x-show="profileOpen"
                        @click.away="profileOpen = false"
                        x-cloak
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md overflow-hidden shadow-xl z-20"
                    >
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Seu Perfil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Alterar Senha</a>

                        <a href="#"  class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Logout</a>
                        <form id="logout-form" action="{{ route('filament.adm.auth.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                    </div>
                </div>
            </div>
        </header>


        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 lg:px-8">
{{--            <h1 class="text-2xl text-slate-800 dark:text-slate-50 mb-4"><i class="fa-solid fa-gauge"></i> Painel de Atividades de <span class="text-2xl text-blue-700 dark:text-yellow-500">{{ $userName }}</span> </h1>--}}
            <!-- Breadcrumb e conteúdo principal aqui -->
            <x-dashboard.breadcrumbs :breadcrumbs="$breadcrumbs"/>
    {{--        {{ $breadcrumbs ? json_encode($breadcrumbs) : 'Breadcrumbs não definidos' }}--}}
            <hr class="mt-2 dark:border-slate-600">

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <!-- ... outros elementos da dashboard ... -->

                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">
                        <i class="fas fa-user-circle mr-2"></i>Foto de Perfil
                    </h2>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <livewire:profile-photo-uploader />
                    </div>
                </div>

                <!-- ... -->
            </div>


        </main>
    </div>

</div>
