<div>
    @if ($step === 'cpf')
        <form wire:submit.prevent="updatedCpf">
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" wire:model="cpf">
            @error('cpf') <span class="error">{{ $message }}</span> @enderror
            <button type="submit">Verificar CPF</button>
        </form>
    @elseif ($step === 'cpf_not_found' || $step === 'user_exists' || $step === 'data_inconsistent')
        <p>{{ $message }}</p>
        <button wire:click="$set('step', 'cpf')">Voltar</button>
    @elseif ($step === 'verify_data')
        <form wire:submit.prevent="verifyData">
            <label for="dataNascimento">Data de Nascimento:</label>
            <input type="date" id="dataNascimento" wire:model="dataNascimento">
            @error('dataNascimento') <span class="error">{{ $message }}</span> @enderror

            <label for="matricula">Matrícula:</label>
            <input type="text" id="matricula" wire:model="matricula">
            @error('matricula') <span class="error">{{ $message }}</span> @enderror

            <button type="submit">Verificar Dados</button>
        </form>
    @elseif ($step === 'create_account')
        <form wire:submit.prevent="createAccount">
            <label for="email">E-mail (opcional):</label>
            <input type="email" id="email" wire:model="email">
            @error('email') <span class="error">{{ $message }}</span> @enderror

            <label for="password">Senha:</label>
            <input type="password" id="password" wire:model="password">
            @error('password') <span class="error">{{ $message }}</span> @enderror

            <label for="password_confirmation">Confirmar Senha:</label>
            <input type="password" id="password_confirmation" wire:model="password_confirmation">

            <button type="submit" @if($errors->has('email')) disabled @endif>Criar Conta</button>
        </form>
    @endif
</div>
