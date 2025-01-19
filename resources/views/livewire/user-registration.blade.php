<section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center px-4 py-8 mx-auto md:h-screen lg:py-0">
        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    {{ $tituloPagina }} <span style="color: dodgerblue;"> {{ $nomePessoa ? ' PARA: '. $nomePessoa : '' }}</span>
                </h1>

                @include("livewire.user-registration.{$step}")

                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Já tem uma conta?
                    <a href="{{ route('filament.adm.auth.login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">
                        Entre aqui
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>
