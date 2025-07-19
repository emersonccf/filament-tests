<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\AlocacaoVeiculoResource\Pages;
use App\Filament\Sevop\Resources\AlocacaoVeiculoResource\RelationManagers;
use App\Models\AlocacaoVeiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlocacaoVeiculoResource extends Resource
{
    protected static ?string $model = AlocacaoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_veiculo')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('id_unidade')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('data_inicio')
                    ->required(),
                Forms\Components\DatePicker::make('data_fim'),
                Forms\Components\Textarea::make('observacoes')
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('id_veiculo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_unidade')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_fim')
                    ->date()
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
            'index' => Pages\ListAlocacaoVeiculos::route('/'),
            'create' => Pages\CreateAlocacaoVeiculo::route('/create'),
            'edit' => Pages\EditAlocacaoVeiculo::route('/{record}/edit'),
        ];
    }
}
