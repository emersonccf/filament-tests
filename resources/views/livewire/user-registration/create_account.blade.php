<form wire:submit.prevent="createAccount" class="space-y-4 md:space-y-6">
    <x-input
        type="text"
        name="cpf"
        label="CPF"
        wire:model="cpf"
        disabled
    />
    <x-input
        type="date"
        name="dataNascimento"
        label="Data de Nascimento"
        wire:model="dataNascimento"
        disabled
    />
    <x-input
        type="text"
        name="matricula"
        label="Matrícula"
        wire:model="matricula"
        disabled
    />
    <x-input
        type="email"
        name="email"
        label="E-mail"
        wire:model="email"
    />
    <x-password-input
        name="password"
        label="Senha"
        placeholder="Informe sua senha"
        required
        wire:model="password"
    />

    <x-password-input
        name="password_confirmation"
        label="Confirmar Senha"
        placeholder="Confirme sua senha"
        required
        wire:model="password_confirmation"
    />

    <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
        CRIAR MINHA CONTA
    </button>
</form>
