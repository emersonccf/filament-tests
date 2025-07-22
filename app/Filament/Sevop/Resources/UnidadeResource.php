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
                Forms\Components\TextInput::make('nome_unidade')
                    ->required()
                    ->maxLength(100)
                    ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                Forms\Components\TextInput::make('codigo_unidade')
                    ->required()
                    ->maxLength(20)
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
                Tables\Columns\TextColumn::make('nome_unidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('codigo_unidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsavel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cadastrado_por')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-M-Y H:i:s')
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
            'index' => Pages\ListUnidades::route('/'),
            'create' => Pages\CreateUnidade::route('/create'),
            'edit' => Pages\EditUnidade::route('/{record}/edit'),
        ];
    }
}
