<form wire:submit.prevent="verifyData" class="space-y-4 md:space-y-6">
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
        required
    />
    <x-input
        type="text"
        name="matricula"
        label="Matrícula"
        wire:model="matricula"
        x-data="numberOnly()"
        x-model="value"
        x-on:input="onInput"
        required
    />
    <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
        VALIDAR DADOS
    </button>
</form>
