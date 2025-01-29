@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-[#006eb6] text-white py-4 px-6">
                <h2 class="text-2xl font-bold">Ativação de Conta Necessária</h2>
            </div>

            <div class="p-6">
                <p class="mb-4">Sua conta foi criada com sucesso, mas precisa ser ativada pelo administrador do sistema.</p>
                <p class="mb-4">Por favor, entre em contato com o administrador para ativar sua conta.</p>
                <p class="mb-4">Isso é necessário pois não foi informado um e-mail para realizar a ativação.</p>
                <p class="mb-4">Informações de contato do administrador:</p>
                <ul class="list-disc pl-5 mb-4">
                    <li>Email: email@test.com</li>
{{--                    <li>Email: {{ config('mail.from.address', 'mail@test.com') }}</li>--}}
                    <li>Telefone: (71) 99999-1010</li>
{{--                    <li>Telefone: (71) 3202-8558</li>--}}
                </ul>
                <a href="{{ route('home') }}" class="inline-block bg-[#006eb6] text-white py-2 px-4 rounded hover:bg-[#005a94] transition duration-300">
                    Voltar para a Página Inicial
                </a>
            </div>
        </div>
    </div>
@endsection
