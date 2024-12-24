<?php
// Fonte: https://laraveldaily.com/post/filament-3-login-with-name-username-or-email

namespace App\Providers\Filament\Auth;

use Filament\Pages\Auth\Login as BaseAuth;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //$this->getEmailFormComponent(),
                $this->getCpfFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getCpfFormComponent(): Component
    {
        return TextInput::make('cpf')
            ->label('CPF ou E-mail')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    
    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['cpf'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'cpf';

        return [
            $login_type => $data['cpf'],
            'password'  => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.cpf' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

}
