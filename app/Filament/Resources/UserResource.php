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
use Illuminate\Support\Facades\Hash;


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
                    ->rule(function ($state, $get) {
                        return [new CpfValido($get('id'))]; // Utiliza o ID do registro atual
                    })
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

                Forms\Components\TextInput::make('password_confirmation')
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
                    //->since(),
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

}
