<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Rules\CpfValido;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @throws ValidationException
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Se a senha estiver vazia, remova-a para não salvar como nulo
        if (empty($data['password'])) {
            unset($data['password']);
            unset($data['password_confirmation']);
        } else {
            // Se a senha for fornecida, criptografe-a antes de salvar
            //$data['password'] = bcrypt($data['password']);
            //$data['password_confirmation'] = bcrypt($data['password_confirmation']);
        }
        //dd($data);
        validator($data,
            [
            'name' => 'required|string|max:255',
            //'cpf' => [new CpfValido(), 'unique:users,cpf,' . ($data['id'] ?? '')],
            //'email' => 'required|email|max:255|unique:users,email,' . ($data['id'] ?? ''),
            'password' => 'nullable|string|min:8|same:password_confirmation'
            ])->validate();
        dd($data);
        return $data;
    }

}
