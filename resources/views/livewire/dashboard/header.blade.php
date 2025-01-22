<header
    class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 fixed top-0 right-0 left-0 h-14 flex items-center transition-colors duration-300"
    x-ref="header"
>
    <!-- Toggle Dark Mode -->
    <button
        @click="darkMode = !darkMode"
        class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200"
    >
        <span x-show="!darkMode"><i class="fas fa-moon"></i></span>
        <span x-show="darkMode"><i class="fas fa-sun"></i></span>
    </button>

    <!-- ... outros elementos do header ... -->
</header>
