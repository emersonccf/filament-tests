<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\VeiculoResource\Pages;
use App\Filament\Sevop\Resources\VeiculoResource\RelationManagers;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('placa')
                    ->maxLength(8)
                    ->default(null),
                Forms\Components\TextInput::make('id_modelo')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('prefixo_veiculo')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('direcionamento')
                    ->required()
                    ->maxLength(20)
                    ->default('NORMAL'),
                Forms\Components\TextInput::make('local_ativacao')
                    ->required()
                    ->maxLength(50)
                    ->default('GTRAN'),
                Forms\Components\TextInput::make('combustivel')
                    ->required()
                    ->maxLength(20)
                    ->default('FLEX'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(20)
                    ->default('ATIVO'),
                Forms\Components\TextInput::make('quilometragem')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\DatePicker::make('data_recebimento'),
                Forms\Components\TextInput::make('chassi')
                    ->maxLength(17)
                    ->default(null),
                Forms\Components\TextInput::make('renavam')
                    ->maxLength(11)
                    ->default(null),
                Forms\Components\TextInput::make('ano_fabricacao'),
                Forms\Components\TextInput::make('ano_modelo'),
                Forms\Components\TextInput::make('cor')
                    ->maxLength(30)
                    ->default(null),
                Forms\Components\TextInput::make('valor_diaria')
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
                Tables\Columns\TextColumn::make('placa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_modelo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prefixo_veiculo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direcionamento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('local_ativacao')
                    ->searchable(),
                Tables\Columns\TextColumn::make('combustivel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quilometragem')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_recebimento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chassi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('renavam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ano_fabricacao'),
                Tables\Columns\TextColumn::make('ano_modelo'),
                Tables\Columns\TextColumn::make('cor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('valor_diaria')
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
            'index' => Pages\ListVeiculos::route('/'),
            'create' => Pages\CreateVeiculo::route('/create'),
            'edit' => Pages\EditVeiculo::route('/{record}/edit'),
        ];
    }
}
