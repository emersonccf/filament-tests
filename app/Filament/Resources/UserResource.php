<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Rules\ContemCaracteresEspeciais;
use App\Rules\ContemLetrasMaiusculas;
use App\Rules\ContemLetrasMinusculas;
use App\Rules\ContemNumeros;
use App\Rules\CpfValido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuário';

    protected static ?string $modelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->placeholder('informe o nome completo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cpf')
                    ->label('CPF')
                    ->placeholder('informe o CPF')
                    ->mask('999.999.999-99')
                    ->required()
//                    ->unique(ignoreRecord: true, modifyRuleUsing : function (Unique $rule) { #TODO: não é possível acessar o valor do campo dentro da closure, que pena...
//                        return $rule->where('cpf', preg_replace('/[^0-9]/', '', $value));
//                    })
//                    ->validationMessages([
//                        'unique' => "Este :attribute já está cadastrado para um usuário do sistema.",
//                    ])
                    ->rule([new CpfValido]) // Aplicação da regra personalizada para o CPF
                    ->maxLength(14),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->placeholder('informe o e-mail')
                    ->email()
                    ->rule(['email' => 'regex:/^.+@.+$/i'])
                    ->required()
                    ->unique(ignoreRecord: true) // ignora o próprio registo na verificação de unicidade
                    ->validationMessages([
                        'unique' => 'Este :attribute já está sendo utilizado por um usuário cadastrado no sistema.',
                        'email' => 'O :attribute informado não é válido'
                    ])
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->placeholder('informe a senha')
                    ->label('Senha')
                    ->password()
                    ->revealable()
                    ->rule([
                        'min' => 'min:8',
                        new ContemLetrasMinusculas(),
                        new ContemLetrasMaiusculas(),
                        new ContemNumeros(),
                        new ContemCaracteresEspeciais(),
                    ]) // Aplicação de regras para a senha
                    ->required()
                    ->validationMessages([
                        'min' => 'A :attribute deve ter no mínimo 8 caracteres entre letras minúsculas, Maiúsculas, caractere especial (@,#,$,%,...) e números',
                    ])
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d-M-Y H:i:s')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated([25, 50, 75, 100, 'all'])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * @throws ValidationException
     */
    public static function mutateFormDataBeforeSave(array $data): array
    {
        // Aplicar lógica ou validação adicional antes de salvar (tanto criar quanto atualizar)
        validator($data, [
            'name' => 'required|string|max:255',
            'cpf' => [new CpfValido, 'unique:users,cpf'. $data['id']],
            'email' => 'required|email|max:255|unique:users,email,' . $data['id'],
            'password' => 'required|string|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ])->validate();

        return $data;
    }

}
