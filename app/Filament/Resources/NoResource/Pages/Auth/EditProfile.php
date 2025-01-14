<?php

namespace App\Filament\Resources\NoResource\Pages\Auth;

use App\Rules\ContemCaracteresEspeciais;
use App\Rules\ContemLetrasMaiusculas;
use App\Rules\ContemLetrasMinusculas;
use App\Rules\ContemNumeros;
use App\Rules\CpfValido;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Hash;

class EditProfile extends BaseEditProfile
{

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //$this->getNameFormComponent(),
                TextInput::make('name')
                    ->label('Nome')
                    ->placeholder('informe o nome completo')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                TextInput::make('cpf')
                    ->label('CPF')
                    ->readOnly()
                    ->placeholder('informe o CPF')
                    ->mask('999.999.999-99')
                    ->required()
                    ->rule(function ($state, $get) {
                        return [new CpfValido($get('id'))]; // Utiliza o ID do registro atual
                    }),
                $this->getEmailFormComponent(),
                TextInput::make('password')
                    ->placeholder('informe a senha')
                    ->label('Senha')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state) ) //fazer o hash da senha quando o formulário fosse enviado
                    ->dehydrated(fn ($state) => filled($state)) // Para não substituir a senha existente se o campo estiver vazio
                    ->required(fn (string $context): bool => $context === 'create') //exigir que a senha seja preenchida na página Criar de um recurso do painel de administração
                    ->confirmed()
                    ->revealable()
                    ->rule([
                        'min:8',
                        'nullable', // Permite que o campo seja opcional durante a edição
                        new ContemLetrasMinusculas(),
                        new ContemLetrasMaiusculas(),
                        new ContemNumeros(),
                        new ContemCaracteresEspeciais(),
                    ]) // Aplicação de regras para a senha
                    ->validationMessages([
                        'min' => 'A :attribute deve ter no mínimo 8 caracteres entre letras minúsculas, Maiúsculas, caractere especial (@,#,$,%,...) e números',
                        'confirmed' => 'Preencha a Confirmação da Senha. Ela não foi informada ou é diferente da Senha!',
                    ])
                    ->maxLength(255),
                TextInput::make('password_confirmation')
                    ->placeholder('Confirme a senha')
                    ->label('Confirmação de Senha')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state) ) //fazer o hash da senha quando o formulário fosse enviado
                    ->dehydrated(fn ($state) => filled($state)) // Para não substituir a senha existente se o campo estiver vazio
                    ->required(fn (string $context): bool => $context === 'create') //exigir que a senha seja preenchida na página Criar de um recurso do painel de administração
                    ->same('password')
                    ->validationMessages([
                        'same' => 'A :attribute é diferente da Senha, faça a correção!',
                    ])
                    ->revealable()
                    ->maxLength(255),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return 'users'; //TODO: Definir para qual rota irá encaminha quando a atualização for realizada
    }

}

