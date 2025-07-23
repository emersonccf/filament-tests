<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\MarcaResource\Pages;
use App\Filament\Sevop\Resources\MarcaResource\RelationManagers;
use App\Models\Marca;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; // Garanta que este import está presente
use Filament\Forms\Components\Placeholder; // Adicione este import
use Filament\Forms\Components\Hidden; // Adicione este import
use Illuminate\Support\Facades\Auth; // Adicione este import para usar Auth::user()
use Filament\Forms\Components\Section;    // Para organizar melhor o formulário

class MarcaResource extends Resource
{
    protected static ?string $model = Marca::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 10; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nome_marca')
                    ->required()
                    ->maxLength(50)
                    ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),

                // Campos ocultos para 'cadastrado_por' e 'atualizado_por'.
                // Estes são importantes se você estiver usando mutateFormDataBeforeCreate/Save
                // ou se o Observer precisar que eles estejam no payload do formulário.
                Hidden::make('cadastrado_por'),
                Hidden::make('atualizado_por'),

                // Placeholder para 'Cadastrado Por'
                Placeholder::make('cadastrado_por_display')
                    ->label('Cadastrado Por')
                    ->content(function (string $operation, ?Marca $record): string {
                        // Se for uma operação de criação (novo registro)
                        if ($operation === 'create') {
                            return Auth::user()->name; // Exibe o nome do usuário logado
                        }
                        // Se for uma operação de edição (registro existente)
                        // Exibe o nome do usuário relacionado, ou 'N/A' se não encontrado
                        return $record?->userCreatedBy?->name ?? 'N/A';
                    })
                    ->columnSpan(1), // Ocupa 1 coluna. Ajuste conforme seu layout.

                // Placeholder para 'Atualizado Por'
                Placeholder::make('atualizado_por_display')
                    ->label('Atualizado Por')
                    ->content(function (string $operation, ?Marca $record): string {
                        // Se for uma operação de criação (novo registro)
                        if ($operation === 'create') {
                            return Auth::user()->name; // Exibe o nome do usuário logado
                        }
                        // Se for uma operação de edição (registro existente)
                        // Exibe o nome do usuário relacionado, ou 'N/A' se não encontrado
                        return $record?->userUpdatedBy?->name ?? 'N/A';
                    })
                    ->columnSpan(1), // Ocupa 1 coluna. Ajuste conforme seu layout.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome_marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('userCreatedBy.name') // OK para tabelas
                ->label('Cadastrado Por')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userUpdatedBy.name') // OK para tabelas
                ->label('Atualizado Por')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-M-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMarcas::route('/'),
            'create' => Pages\CreateMarca::route('/create'),
            'edit' => Pages\EditMarca::route('/{record}/edit'),
        ];
    }
}
