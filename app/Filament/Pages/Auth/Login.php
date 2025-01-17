<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('Cadastre-se aqui!'))
            ->url(route('register'));
    }

}
