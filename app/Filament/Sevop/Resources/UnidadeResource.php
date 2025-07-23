<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\UnidadeResource\Pages;
use App\Filament\Sevop\Resources\UnidadeResource\RelationManagers;
use App\Models\Unidade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


// NOVOS IMPORTS NECESSÁRIOS:
use Filament\Forms\Components\TextInput; // Garanta que este import está presente
use App\Models\User;   // Para as relações de usuário (cadastrado_por, atualizado_por)
use Filament\Forms\Components\Placeholder; // Para exibir informações não editáveis (nome do usuário)
use Filament\Forms\Components\Hidden;     // Para ocultar campos preenchidos automaticamente
use Illuminate\Support\Facades\Auth;      // Para acessar o usuário logado
use Filament\Forms\Components\Section;    // Para organizar melhor o formulário

class UnidadeResource extends Resource
{
    protected static ?string $model = Unidade::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 40; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações da Unidade') // Adiciona uma seção para organização
                ->schema([
                    Forms\Components\TextInput::make('nome_unidade')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true) // Adicionado para garantir nome único
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                    Forms\Components\TextInput::make('codigo_unidade')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true) // Adicionado para garantir código único
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                    Forms\Components\TextInput::make('telefone')
                        ->tel()
                        ->mask('(99) 99999-9999')
                        ->maxLength(15)
                        ->default(null),
                    Forms\Components\TextInput::make('responsavel')
                        ->maxLength(100)
                        ->default(null)
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                ])->columns(2), // Define 2 colunas para esta seção

                Section::make('Informações de Auditoria') // Nova seção para campos de auditoria
                ->schema([
                    // Campos ocultos para 'cadastrado_por' e 'atualizado_por'.
                    // Estes são preenchidos automaticamente pelo Observer.
                    Hidden::make('cadastrado_por'),
                    Hidden::make('atualizado_por'),

                    // Placeholder para 'Cadastrado Por' - Exibe o nome do usuário, não editável.
                    Placeholder::make('cadastrado_por_display')
                        ->label('Cadastrado Por')
                        ->content(function (string $operation, ?Unidade $record): string {
                            if ($operation === 'create') {
                                return Auth::user()->name; // Exibe o nome do usuário logado na criação
                            }
                            // Busca o nome do usuário através da relação no Model
                            return $record?->userCreatedBy?->name ?? 'N/A';
                        })
                        ->columnSpan(1),

                    // Placeholder para 'Atualizado Por' - Exibe o nome do usuário, não editável.
                    Placeholder::make('atualizado_por_display')
                        ->label('Atualizado Por')
                        ->content(function (string $operation, ?Unidade $record): string {
                            if ($operation === 'create') {
                                return Auth::user()->name; // Exibe o nome do usuário logado na criação
                            }
                            // Busca o nome do usuário através da relação no Model
                            return $record?->userUpdatedBy?->name ?? 'N/A';
                        })
                        ->columnSpan(1),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome_unidade')
                    ->searchable()
                    ->sortable(), // Adicionado sortable
                Tables\Columns\TextColumn::make('codigo_unidade')
                    ->searchable()
                    ->sortable(), // Adicionado sortable
                Tables\Columns\TextColumn::make('telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsavel')
                    ->searchable(),
                // Exibindo o nome do usuário em vez do ID
                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y H:i:s') // Formato padrão de data/hora do Filament
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Exibindo o nome do usuário em vez do ID
                Tables\Columns\TextColumn::make('userUpdatedBy.name')
                    ->label('Atualizado Por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-M-Y H:i:s') // Formato padrão de data/hora do Filament
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Não há filtros específicos para Enums ou outras chaves estrangeiras aqui.
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListUnidades::route('/'),
            'create' => Pages\CreateUnidade::route('/create'),
            'edit' => Pages\EditUnidade::route('/{record}/edit'),
        ];
    }
}
