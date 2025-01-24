<x-filament-panels::page>
    <div>
        <h2>Verifique seu endereço de e-mail</h2>
        <p>Você deve verificar seu endereço de e-mail para acessar esta página.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">Reenviar e-mail de verificação</button>
        </form>
    </div>
</x-filament-panels::page>
