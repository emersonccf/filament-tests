<form wire:submit.prevent="updatedCpf" class="space-y-4 md:space-y-6">
    <x-input
        type="text"
        name="cpf"
        label="CPF"
        placeholder="Informe o CPF"
        wire:model.live.debounce.250ms="cpf"
        x-data="{ cpf: '' }"
        x-model="cpf"
        x-on:input="cpf = cpfMask($event.target.value)"
        required
        autofocus
    />
</form>
