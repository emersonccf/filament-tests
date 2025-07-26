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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;

class MarcaResource extends Resource
{
    protected static ?string $model = Marca::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Controle de Frota';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações da Marca')
                    ->description('Dados principais da marca')
                    ->schema([
                        TextInput::make('nome_marca')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state))
                            ->helperText('O nome será convertido automaticamente para maiúsculas'),
                    ])
                    ->columns(1)
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
                                        ->content(function (string $operation, ?Marca $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userCreatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('created_at_display')
                                        ->label('Data de Criação')
                                        ->content(function (string $operation, ?Marca $record): string {
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
                                        ->content(function (string $operation, ?Marca $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userUpdatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('updated_at_display')
                                        ->label('Última Atualização')
                                        ->content(function (string $operation, ?Marca $record): string {
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
            ->defaultSort('nome_marca', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('nome_marca')
                    ->label('Nome da Marca')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

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
            'index' => Pages\ListMarcas::route('/'),
            'create' => Pages\CreateMarca::route('/create'),
            'edit' => Pages\EditMarca::route('/{record}/edit'),
        ];
    }
}
