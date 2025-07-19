<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\ModeloResource\Pages;
use App\Filament\Sevop\Resources\ModeloResource\RelationManagers;
use App\Models\Modelo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModeloResource extends Resource
{
    protected static ?string $model = Modelo::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 20; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_marca')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nome_modelo')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('categoria')
                    ->required()
                    ->maxLength(50)
                    ->default('OUTROS'),
                Forms\Components\TextInput::make('numero_portas')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('capacidade_passageiros')
                    ->required()
                    ->numeric()
                    ->default(2),
                Forms\Components\TextInput::make('numero_rodas')
                    ->required()
                    ->numeric()
                    ->default(4),
                Forms\Components\TextInput::make('cilindrada')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('peso_bruto')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('cadastrado_por')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('atualizado_por')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_marca')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nome_modelo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria')
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_portas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacidade_passageiros')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_rodas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cilindrada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('peso_bruto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cadastrado_por')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('atualizado_por')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListModelos::route('/'),
            'create' => Pages\CreateModelo::route('/create'),
            'edit' => Pages\EditModelo::route('/{record}/edit'),
        ];
    }
}
