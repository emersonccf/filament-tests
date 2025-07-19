<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\HistoricoVeiculoResource\Pages;
use App\Filament\Sevop\Resources\HistoricoVeiculoResource\RelationManagers;
use App\Models\HistoricoVeiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoricoVeiculoResource extends Resource
{
    protected static ?string $model = HistoricoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 60; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_veiculo')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tipo_evento')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('data_evento')
                    ->required(),
                Forms\Components\TextInput::make('hora_evento'),
                Forms\Components\TextInput::make('quilometragem')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('prioridade')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\Toggle::make('afeta_disponibilidade')
                    ->required(),
                Forms\Components\TextInput::make('status_evento')
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('descricao')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('local_ocorrencia')
                    ->maxLength(200)
                    ->default(null),
                Forms\Components\TextInput::make('prestador_servico')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\DatePicker::make('data_prevista_conclusao'),
                Forms\Components\DatePicker::make('data_conclusao'),
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
                Tables\Columns\TextColumn::make('tipo_evento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_evento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora_evento'),
                Tables\Columns\TextColumn::make('quilometragem')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prioridade')
                    ->searchable(),
                Tables\Columns\IconColumn::make('afeta_disponibilidade')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status_evento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('local_ocorrencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prestador_servico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_prevista_conclusao')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_conclusao')
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
            'index' => Pages\ListHistoricoVeiculos::route('/'),
            'create' => Pages\CreateHistoricoVeiculo::route('/create'),
            'edit' => Pages\EditHistoricoVeiculo::route('/{record}/edit'),
        ];
    }
}
