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
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;
use App\Models\Marca;
use App\Enums\CategoriaVeiculo;

class ModeloResource extends Resource
{
    protected static ?string $model = Modelo::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Controle de Frota';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Modelo')
                    ->description('Dados principais do modelo de veículo')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                // Linha 1: Marca, Categoria e Nome do Modelo
                                Select::make('id_marca')
                                    ->label('Marca do Veículo')
                                    ->relationship('marca', 'nome_marca')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nome_marca')
                                            ->required()
                                            ->maxLength(50)
                                            ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                                    ])
                                    ->columnSpan(1),

                                Select::make('categoria')
                                    ->label('Categoria do Veículo')
                                    ->options(CategoriaVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                TextInput::make('nome_modelo')
                                    ->label('Nome do Modelo')
                                    ->required()
                                    ->maxLength(50)
                                    ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state))
                                    ->helperText('Nome será convertido para maiúsculas')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Especificações Técnicas')
                    ->description('Características técnicas e capacidades do veículo')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                // Linha 1: Quilometragem, Portas e Passageiros
                                TextInput::make('quilometragem_revisao')
                                    ->label('Quilometragem para Revisão (Km)')
                                    ->required()
                                    ->numeric()
                                    ->default(10000.00)
                                    ->step(1000.00)
                                    ->suffix('Km')
                                    ->columnSpan(1),

                                TextInput::make('numero_portas')
                                    ->label('Número de Portas')
                                    ->required()
                                    ->numeric()
                                    ->default(4)
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->columnSpan(1),

                                TextInput::make('capacidade_passageiros')
                                    ->label('Capacidade de Passageiros')
                                    ->required()
                                    ->numeric()
                                    ->default(5)
                                    ->minValue(1)
                                    ->maxValue(50)
                                    ->columnSpan(1),

                                // Linha 2: Rodas, Cilindrada e Peso
                                TextInput::make('numero_rodas')
                                    ->label('Número de Rodas')
                                    ->required()
                                    ->numeric()
                                    ->default(4)
                                    ->minValue(2)
                                    ->maxValue(20)
                                    ->columnSpan(1),

                                TextInput::make('cilindrada')
                                    ->label('Cilindrada')
                                    ->maxLength(10)
                                    ->placeholder('Ex: 1.0, 2.0, V8')
                                    ->columnSpan(1),

                                TextInput::make('peso_bruto')
                                    ->label('Peso Bruto (Kg)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('Kg')
                                    ->placeholder('Ex: 1500.50')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Informações de Auditoria')
                    ->description('Dados de controle e rastreabilidade')
                    ->schema([
                        // Campos ocultos para preenchimento automático
                        Hidden::make('cadastrado_por'),
                        Hidden::make('atualizado_por'),

                        // Grid para organizar as informações de auditoria
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Coluna 1: Informações de criação
                                Forms\Components\Group::make([
                                    Placeholder::make('cadastrado_por_display')
                                        ->label('Cadastrado Por')
                                        ->content(function (string $operation, ?Modelo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userCreatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('created_at_display')
                                        ->label('Data de Criação')
                                        ->content(function (string $operation, ?Modelo $record): string {
                                            if ($operation === 'create') {
                                                return 'Será definida ao salvar';
                                            }
                                            return $record?->created_at?->format('d/m/Y H:i:s') ?? 'N/A';
                                        }),
                                ])->columnSpan(1),

                                // Coluna 2: Informações de atualização
                                Forms\Components\Group::make([
                                    Placeholder::make('atualizado_por_display')
                                        ->label('Atualizado Por')
                                        ->content(function (string $operation, ?Modelo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userUpdatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('updated_at_display')
                                        ->label('Última Atualização')
                                        ->content(function (string $operation, ?Modelo $record): string {
                                            if ($operation === 'create') {
                                                return 'Será definida ao salvar';
                                            }
                                            return $record?->updated_at?->format('d/m/Y H:i:s') ?? 'N/A';
                                        }),
                                ])->columnSpan(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 10])
            ->defaultSort('nome_modelo', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('marca.nome_marca')
                    ->label('Marca')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('nome_modelo')
                    ->label('Modelo')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('categoria')
                    ->label('Categoria')
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('quilometragem_revisao')
                    ->label('Km Revisão')
                    ->numeric(0)
                    ->sortable()
                    ->suffix(' Km')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('numero_portas')
                    ->label('Portas')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('capacidade_passageiros')
                    ->label('Passageiros')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('numero_rodas')
                    ->label('Rodas')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cilindrada')
                    ->label('Cilindrada')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('peso_bruto')
                    ->label('Peso Bruto')
                    ->numeric(2)
                    ->sortable()
                    ->suffix(' Kg')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('userUpdatedBy.name')
                    ->label('Atualizado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Atualização')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_marca')
                    ->label('Filtrar por Marca')
                    ->relationship('marca', 'nome_marca')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('categoria')
                    ->label('Filtrar por Categoria')
                    ->options(CategoriaVeiculo::class)
                    ->multiple(),

                Tables\Filters\Filter::make('capacidade_passageiros')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('capacidade_min')
                                    ->label('Capacidade Mínima')
                                    ->numeric()
                                    ->placeholder('Ex: 2'),
                                Forms\Components\TextInput::make('capacidade_max')
                                    ->label('Capacidade Máxima')
                                    ->numeric()
                                    ->placeholder('Ex: 50'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['capacidade_min'],
                                fn (Builder $query, $value): Builder => $query->where('capacidade_passageiros', '>=', $value),
                            )
                            ->when(
                                $data['capacidade_max'],
                                fn (Builder $query, $value): Builder => $query->where('capacidade_passageiros', '<=', $value),
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Criado a partir de'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Criado até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
