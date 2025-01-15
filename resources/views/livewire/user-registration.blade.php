<section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center px-4 py-8 mx-auto md:h-screen lg:py-0"> {{--  justify-center --}}
{{--        <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">--}}
{{--            <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">--}}
{{--            Seu Logo--}}
{{--        </a>--}}
        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    {{ $titulo }} <span style="color: yellow;">{{ $nomePessoa? ' PARA: '. $nomePessoa: '' }} </span>
                </h1>

                @if ($step === 'cpf')
                    <form class="space-y-4 md:space-y-6">
                        <div>
                            <label for="cpf" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CPF</label>
                            <input type="text" id="cpf" wire:model.live.debounce.250ms="cpf" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="informe o cpf" required>
                            @error('cpf') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </form>
                @elseif ($step === 'cpf_not_found' || $step === 'user_exists' || $step === 'data_inconsistent')
                    <p class="text-red-600">{{ $message }}</p>
                    <button wire:click="$set('step', 'cpf')" class="w-full text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Voltar
                    </button>
                @elseif ($step === 'verify_data')
                    <form class="space-y-4 md:space-y-6">
                        <div>
                            <label for="cpf" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CPF</label>
                            <input type="text" id="cpf" wire:model.live.debounce.250ms="cpf" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="informe o cpf" required>
                            @error('cpf') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </form>

                    <form wire:submit.prevent="verifyData" class="space-y-4 md:space-y-6">
                      <div>
                            <label for="dataNascimento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Data de Nascimento</label>
                            <input type="date" id="dataNascimento" wire:model="dataNascimento" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            @error('dataNascimento') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="matricula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matrícula (apenas números)</label>
                            <input type="text" id="matricula" wire:model="matricula" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            @error('matricula') class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit"  class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            VALIDAR DADOS
                        </button>
                    </form>

                @elseif ($step === 'create_account')
                    <form class="space-y-4 md:space-y-6">
                        <div>
                            <label for="cpf" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CPF</label>
                            <input type="text" id="cpf" wire:model.live.debounce.250ms="cpf" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="informe o cpf" required>
                            @error('cpf') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </form>

                    <form class="space-y-4 md:space-y-6">
                        <div>
                            <label for="dataNascimento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Data de Nascimento</label>
                            <input type="date" id="dataNascimento" wire:model="dataNascimento" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            @error('dataNascimento') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="matricula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matrícula (apenas números)</label>
                            <input type="text" id="matricula" wire:model="matricula"  disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            @error('matricula') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </form>


                    <form wire:submit.prevent="createAccount" class="space-y-4 md:space-y-6">

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail (opcional)</label>
                            <input type="email" id="email" wire:model="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="informe seu e-mail...">
                            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Senha</label>
                            <input type="password" id="password" wire:model="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"  placeholder="informe a senha" required>
                            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirmar Senha</label>
                            <input type="password" id="password_confirmation" wire:model="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="confirme a senha" required>
                        </div>
                        <div class="flex items-start">
{{--                            <div class="flex items-center h-5">--}}
{{--                                <input id="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required>--}}
{{--                            </div>--}}
{{--                            <div class="ml-3 text-sm">--}}
{{--                                <label for="terms" class="font-light text-gray-500 dark:text-gray-300">Eu aceito os <a class="font-medium text-primary-600 hover:underline dark:text-primary-500" href="#">Termos e Condições</a></label>--}}
{{--                            </div>--}}
                        </div>
                        <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            CRIAR MINHA CONTA
                        </button>
                    </form>
                @endif

                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Já tem uma conta? <a href="{{  route('filament.adm.auth.login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Entre aqui</a>
                </p>
            </div>
        </div>
    </div>
</section>
