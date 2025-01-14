<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as AuthRegister;
use Illuminate\Contracts\Support\Htmlable;

class Register extends AuthRegister
{
    //https://www.youtube.com/watch?v=SQcXFUmnnsw
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }



}
