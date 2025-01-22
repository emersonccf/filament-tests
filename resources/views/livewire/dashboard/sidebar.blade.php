<!-- Sidebar -->
<aside
    class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-r border-gray-200 dark:border-gray-700 transition-all duration-300 fixed inset-y-0 left-0 z-20 w-64 lg:w-auto lg:relative lg:translate-x-0"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
>
    <!-- Toggle button -->
    <button
        @click="sidebarOpen = !sidebarOpen"
        class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring"
    >
        <x-heroicon-o-bars-3 class="w-10 h-10" />
    </button>

    <!-- Links do Sidebar -->
    <nav class="mt-5">
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-computer-desktop class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Painel de Atividades
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-chat-bubble-left class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Mensagens
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-heart class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Afastamentos: atestados, licenças medicas, etc.
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-users class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Acordos entre as Partes (Permutas)
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-pause-circle class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Folgas TRE
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-photo class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Férias
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-gift class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Licença Prêmio
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-pencil-square class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Solicitações
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-table-cells class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Escala Mensal
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-newspaper class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Escala Especial
            </span>
        </a>

        <a
            href="#"
            class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'"
        >
            <x-heroicon-s-musical-note class="w-5 h-5 mr-2" />

            <span x-show="sidebarOpen" class="ml-2">
                Carnaval
            </span>
        </a>

    </nav>
</aside>
