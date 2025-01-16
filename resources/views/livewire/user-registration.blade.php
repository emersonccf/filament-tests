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
                    <form wire:submit.prevent="#" class="space-y-4 md:space-y-6">
                        <div x-data="{ cpf: '' }">
                            <label for="cpf" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">CPF</label>
                            <input
                                type="text"
                                id="cpf"
                                name="cpf"
                                wire:model.live.debounce.250ms="cpf"
                                x-model="cpf"
                                @input="cpf = cpfMask($event.target.value)"
                                {{--                                x-on:input="cpf = cpf.replace(/\D/g, '')--}}
                                {{--                                                   .replace(/(\d{3})(\d)/, '$1.$2')--}}
                                {{--                                                   .replace(/(\d{3})(\d)/, '$1.$2')--}}
                                {{--                                                   .replace(/(\d{3})(\d{1,2})$/, '$1-$2')"--}}
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="informe o cpf" required>
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
                        <div x-data="numberOnly()">
                            <label for="matricula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matrícula</label>
                            <input
                                type="text"
                                id="matricula"
                                wire:model="matricula"
                                x-model="value"
                                @input="onInput"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
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
                            <label for="matricula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matrícula</label>
                            <input type="text" id="matricula" wire:model="matricula"  disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            @error('matricula') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </form>


                    <form wire:submit.prevent="createAccount" class="space-y-4 md:space-y-6">

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail</label>
                            <input type="email" id="email" wire:model="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="informe seu e-mail...">
                            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- SENHA -->
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Senha</label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    wire:model="password"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="informe a senha"
                                    required
                                >
                                <button
                                    @click="showPassword = !showPassword"
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-700 dark:text-gray-300"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        :class="{'hidden': showPassword, 'block': !showPassword}"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg
                                        class="w-5 h-5"
                                        :class="{'block': showPassword, 'hidden': !showPassword}"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- CONFIRMAÇÃO DE SENHA -->
                        <div x-data="{ showConfirmPassword: false }">
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirmar Senha</label>
                            <div class="relative">
                                <input
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    id="password_confirmation"
                                    wire:model="password_confirmation"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 pr-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="confirme a senha"
                                    required
                                >
                                <button
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-700 dark:text-gray-300"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        :class="{'hidden': showConfirmPassword, 'block': !showConfirmPassword}"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg
                                        class="w-5 h-5"
                                        :class="{'block': showConfirmPassword, 'hidden': !showConfirmPassword}"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{--                        <div class="flex items-start">--}}
                        {{--                            <div class="flex items-center h-5">--}}
                        {{--                                <input id="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="ml-3 text-sm">--}}
                        {{--                                <label for="terms" class="font-light text-gray-500 dark:text-gray-300">Eu aceito os <a class="font-medium text-primary-600 hover:underline dark:text-primary-500" href="#">Termos e Condições</a></label>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            CRIAR MINHA CONTA
                        </button>
                    </form>
                @endif

                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Já tem uma conta?
                    <a href="{{  route('filament.adm.auth.login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">
                        Entre aqui
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>
