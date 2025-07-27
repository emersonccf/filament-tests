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
use Filament\Forms\Components\TextInput;
use App\Models\Veiculo;
use App\Models\Unidade;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class AlocacaoVeiculoResource extends Resource
{
    protected static ?string $model = AlocacaoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Controle de Frota';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações da Alocação')
                    ->description('Dados principais da alocação de veículo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
//                                Select::make('id_veiculo')
//                                    ->label('Veículo')
//                                    ->relationship(
//                                        name: 'veiculo',
//                                        titleAttribute: 'placa',
//                                        modifyQueryUsing: fn (Builder $query) => $query
//                                            ->with(['modelo', 'modelo.marca'])
//                                            ->where('status', 'ATIVO') // Apenas veículos ativos
//                                    )
//                                    ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => $record->placa_modelo_direcionamento)
//                                    ->required()
//                                    ->searchable()
//                                    ->preload()
//                                    ->helperText('Selecione o veículo a ser alocado')
//                                    ->columnSpan(1),
                                Select::make('id_veiculo')
                                    ->label('Veículo')
                                    ->relationship(
                                        name: 'veiculo',
                                        titleAttribute: 'placa',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->with('modelo')
                                            ->where('status', 'ATIVO') // Apenas veículos ativos
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => $record->placa_modelo_direcionamento)
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Selecione o veículo a ser alocado')
                                    ->columnSpan(1),

                                Select::make('id_unidade')
                                    ->label('Unidade de Destino')
                                    ->relationship('unidade', 'nome_unidade')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Unidade que receberá o veículo')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Período da Alocação')
                    ->description('Definição do período de alocação do veículo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                DatePicker::make('data_inicio')
                                    ->label('Data de Início')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->helperText('Data de início da alocação')
                                    ->columnSpan(1),

                                DatePicker::make('data_fim')
                                    ->label('Data de Fim')
                                    ->displayFormat('d/m/Y')
                                    ->helperText('Deixe em branco para alocação indefinida')
                                    ->after('data_inicio')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Observações e Detalhes')
                    ->description('Informações adicionais sobre a alocação')
                    ->schema([
                        Textarea::make('observacoes')
                            ->label('Observações')
                            ->placeholder('Descreva detalhes importantes sobre esta alocação...')
                            ->maxLength(65535)
                            ->rows(4)
                            ->columnSpanFull(),
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
                                        ->content(function (string $operation, ?AlocacaoVeiculo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userCreatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('created_at_display')
                                        ->label('Data de Criação')
                                        ->content(function (string $operation, ?AlocacaoVeiculo $record): string {
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
                                        ->content(function (string $operation, ?AlocacaoVeiculo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userUpdatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('updated_at_display')
                                        ->label('Última Atualização')
                                        ->content(function (string $operation, ?AlocacaoVeiculo $record): string {
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
            ->paginationPageOptions([5, 10, 20, 50, 100, 'all'])
            ->defaultPaginationPageOption(5)
            ->defaultSort('data_inicio', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa_modelo_direcionamento')
                    ->label('Veículo'),
//                    ->weight('bold')
//                    ->wrap(),

                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyMessage('Placa copiada!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('unidade.nome_unidade')
                    ->label('Unidade')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('unidade.codigo_unidade')
                    ->label('Código Unidade')
                    ->searchable()
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('data_inicio')
                    ->label('Data Início')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-play'),

                Tables\Columns\TextColumn::make('data_fim')
                    ->label('Data Fim')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state ? 'danger' : 'warning')
                    ->icon(fn ($state) => $state ? 'heroicon-o-stop' : 'heroicon-o-clock')
                    ->formatStateUsing(fn ($state) => $state ? $state : 'Indefinida')
                    ->tooltip(fn ($state) => $state ? 'Alocação com data de fim definida' : 'Alocação por tempo indefinido'),

//                Tables\Columns\TextColumn::make('status_alocacao')
//                    ->label('Status')
//                    ->badge()
//                    ->color(fn ($record) => match (true) {
//                        !$record->data_fim => 'success', // Ativa (sem data fim)
//                        $record->data_fim->isFuture() => 'warning', // Ativa (com data fim futura)
//                        $record->data_fim->isPast() => 'danger', // Expirada
//                        default => 'gray'
//                    })
//                    ->formatStateUsing(fn ($record) => match (true) {
//                        !$record->data_fim => 'Ativa',
//                        $record->data_fim->isFuture() => 'Ativa',
//                        $record->data_fim->isPast() => 'Expirada',
//                        default => 'Indefinida'
//                    })
//                    ->icon(fn ($record) => match (true) {
//                        !$record->data_fim => 'heroicon-o-check-circle',
//                        $record->data_fim->isFuture() => 'heroicon-o-clock',
//                        $record->data_fim->isPast() => 'heroicon-o-x-circle',
//                        default => 'heroicon-o-question-mark-circle'
//                    }),

//                Tables\Columns\TextColumn::make('dias_alocacao')
//                    ->label('Duração')
//                    ->formatStateUsing(function ($record) {
//                        $inicio = $record->data_inicio;
//                        $fim = $record->data_fim ?? now();
//                        $dias = $inicio->diffInDays($fim);
//                        return $dias . ' dias';
//                    })
//                    ->badge()
//                    ->color('info')
//                    ->alignCenter()
//                    ->tooltip('Duração da alocação em dias'),

                Tables\Columns\TextColumn::make('observacoes')
                    ->label('Observações')
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Criação')
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
                Tables\Filters\SelectFilter::make('id_veiculo')
                    ->label('Filtrar por Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('id_unidade')
                    ->label('Filtrar por Unidade')
                    ->relationship('unidade', 'nome_unidade')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('status_alocacao')
                    ->label('Status da Alocação')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'ativa' => 'Ativa',
                                'expirada' => 'Expirada',
                                'indefinida' => 'Indefinida',
                            ])
                            ->placeholder('Selecione o status'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['status'],
                            function (Builder $query, $status) {
                                return match ($status) {
                                    'ativa' => $query->where(function ($q) {
                                        $q->whereNull('data_fim')
                                            ->orWhere('data_fim', '>', now());
                                    }),
                                    'expirada' => $query->where('data_fim', '<', now()),
                                    'indefinida' => $query->whereNull('data_fim'),
                                    default => $query
                                };
                            }
                        );
                    }),

                Tables\Filters\Filter::make('data_inicio')
                    ->label('Período de Início')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('data_inicio_from')
                                    ->label('Data de Início (De)')
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\DatePicker::make('data_inicio_until')
                                    ->label('Data de Início (Até)')
                                    ->displayFormat('d/m/Y'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '>=', $date),
                            )
                            ->when(
                                $data['data_inicio_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('data_fim')
                    ->label('Período de Fim')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('data_fim_from')
                                    ->label('Data de Fim (De)')
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\DatePicker::make('data_fim_until')
                                    ->label('Data de Fim (Até)')
                                    ->displayFormat('d/m/Y'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_fim_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_fim', '>=', $date),
                            )
                            ->when(
                                $data['data_fim_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_fim', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('duracao')
                    ->label('Duração da Alocação')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('dias_min')
                                    ->label('Duração Mínima (dias)')
                                    ->numeric()
                                    ->placeholder('Ex: 30'),
                                Forms\Components\TextInput::make('dias_max')
                                    ->label('Duração Máxima (dias)')
                                    ->numeric()
                                    ->placeholder('Ex: 365'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dias_min'],
                                function (Builder $query, $dias) {
                                    return $query->whereRaw('DATEDIFF(COALESCE(data_fim, NOW()), data_inicio) >= ?', [$dias]);
                                }
                            )
                            ->when(
                                $data['dias_max'],
                                function (Builder $query, $dias) {
                                    return $query->whereRaw('DATEDIFF(COALESCE(data_fim, NOW()), data_inicio) <= ?', [$dias]);
                                }
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->label('Data de Cadastro')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Cadastrado a partir de')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Cadastrado até')
                            ->displayFormat('d/m/Y'),
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
                //Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
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
