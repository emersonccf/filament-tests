<?php

namespace App\Filament\Sevop\Resources;

use App\Enums\CategoriaVeiculo;
use App\Enums\LocalidadeAtivacaoTurnoVeiculo;
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
use App\Models\Modelo;
use App\Models\User;
use App\Enums\DirecionamentoVeiculo;
use App\Enums\LocalAtivacaoVeiculo;
use App\Enums\CombustivelVeiculo;
use App\Enums\StatusVeiculo;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Controle de Frota';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identificação do Veículo')
                    ->description('Dados básicos de identificação do veículo')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                TextInput::make('placa')
                                    ->label('Placa')
                                    ->maxLength(8)
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state))
                                    ->placeholder('ABC1234')
                                    ->helperText('Formato: ABC1234')
                                    ->columnSpan(1),

                                Select::make('id_modelo')
                                    ->label('Modelo do Veículo')
                                    ->relationship('modelo', 'nome_modelo')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nome_modelo')
                                            ->required()
                                            ->maxLength(50)
                                            ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                                        Select::make('id_marca')
                                            ->label('Marca')
                                            ->relationship('marca', 'nome_marca')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('categoria')
                                            ->label('Categoria')
                                            ->options(CategoriaVeiculo::class)
                                            ->required()
                                            ->native(false),
                                        TextInput::make('numero_portas')
                                            ->numeric()
                                            ->default(4),
                                    ])
                                    ->columnSpan(1),

                                TextInput::make('prefixo_veiculo')
                                    ->label('Prefixo do Veículo')
                                    ->required()
                                    ->maxLength(10)
                                    ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state))
                                    ->placeholder('SEVOP001')
                                    ->columnSpan(1),

                                Select::make('status')
                                    ->label('Status Operacional')
                                    ->options(StatusVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Configuração Operacional')
                    ->description('Configurações de direcionamento e localização')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Select::make('direcionamento')
                                    ->label('Direcionamento')
                                    ->options(DirecionamentoVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('local_ativacao')
                                    ->label('Local de Ativação')
                                    ->options(LocalAtivacaoVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('localidade_ativacao_mat')
                                    ->label('Localidade Ativação Matutina')
                                    ->options(LocalidadeAtivacaoTurnoVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('localidade_ativacao_vesp')
                                    ->label('Localidade Ativação Vespertina')
                                    ->options(LocalidadeAtivacaoTurnoVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('localidade_ativacao_not')
                                    ->label('Localidade Ativação Noturna')
                                    ->options(LocalidadeAtivacaoTurnoVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Especificações Técnicas')
                    ->description('Dados técnicos e características do veículo')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Select::make('combustivel')
                                    ->label('Tipo de Combustível')
                                    ->options(CombustivelVeiculo::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),

                                TextInput::make('cor')
                                    ->label('Cor')
                                    ->maxLength(30)
                                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                                    $state ? mb_strtoupper($state) : null
                                    )
                                    ->placeholder('BRANCO')
                                    ->columnSpan(1),

                                TextInput::make('ano_fabricacao')
                                    ->label('Ano de Fabricação')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 1)
                                    ->placeholder('2023')
                                    ->columnSpan(1),

                                TextInput::make('ano_modelo')
                                    ->label('Ano do Modelo')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 1)
                                    ->placeholder('2024')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Documentação e Identificação')
                    ->description('Documentos e números de identificação')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('chassi')
                                    ->label('Chassi')
                                    ->maxLength(17)
                                    ->unique(ignoreRecord: true)
                                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                                    $state ? mb_strtoupper($state) : null
                                    )
                                    ->placeholder('9BWZZZ377VT004251')
                                    ->helperText('17 caracteres')
                                    ->columnSpan(1),

                                TextInput::make('renavam')
                                    ->label('RENAVAM')
                                    ->maxLength(11)
                                    ->unique(ignoreRecord: true)
                                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                                    $state ? mb_strtoupper($state) : null
                                    )
                                    ->placeholder('12345678901')
                                    ->helperText('Até 11 dígitos')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Controle de Manutenção')
                    ->description('Informações sobre manutenção e quilometragem')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('quilometragem')
                                    ->label('Quilometragem Atual')
                                    ->required()
                                    ->numeric()
                                    ->default(0.00)
                                    ->step(100.00)
                                    ->suffix('Km')
                                    ->columnSpan(1),

                                TextInput::make('km_proxima_revisao')
                                    ->label('Km da Próxima Revisão')
                                    ->required()
                                    ->numeric()
                                    ->default(10000.00)
                                    ->step(1000)
                                    ->suffix('Km')
                                    ->columnSpan(1),

//                                Toggle::make('revisao_pendente')
//                                    ->label('Revisão Pendente?')
//                                    ->default(false)
//                                    ->helperText('Marque se há revisão pendente')
//                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Equipamentos e Acessórios')
                    ->description('Equipamentos instalados no veículo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Toggle::make('possui_bateria_auxiliar')
                                    ->label('Possui Bateria Auxiliar?')
                                    ->default(false)
                                    ->helperText('Bateria adicional para equipamentos')
                                    ->columnSpan(1),

                                Toggle::make('possui_gps')
                                    ->label('Possui GPS?')
                                    ->default(false)
                                    ->helperText('Sistema de rastreamento GPS')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Dados de Controle do Contrato de Locação')
                    ->description('Controle de recebimento, devolução e valores')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                DatePicker::make('data_recebimento')
                                    ->label('Data de Recebimento')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),

                                DatePicker::make('data_devolucao')
                                    ->label('Data de Devolução')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),
                                TextInput::make('valor_diaria')
                                    ->label('Valor da Diária')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->inputMode('decimal')
                                    ->step(0.01)
                                    ->placeholder('150.00')
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
                                        ->content(function (string $operation, ?Veiculo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userCreatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('created_at_display')
                                        ->label('Data de Criação')
                                        ->content(function (string $operation, ?Veiculo $record): string {
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
                                        ->content(function (string $operation, ?Veiculo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userUpdatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('updated_at_display')
                                        ->label('Última Atualização')
                                        ->content(function (string $operation, ?Veiculo $record): string {
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
            ->defaultSort('prefixo_veiculo', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('veiculo_em_dias_com_revisao')
                    ->label('Revisão OK?')
                    ->formatStateUsing(fn ($state) => $state ? 'SIM' : 'NÃO')
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->badge()
                    ->state(fn ($record) => $record->veiculo_em_dias_com_revisao)
                    ->tooltip(fn ($record) =>
                    "Km Atual: {$record->quilometragem} / Próxima revisão: {$record->km_proxima_revisao}"
                    )
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Placa copiada!')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('modelo.nome_modelo')
                    ->label('Modelo')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('prefixo_veiculo')
                    ->label('Prefixo')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('direcionamento')
                    ->label('Direcionamento')
                    ->searchable()
//                    ->color(fn (string $state): string => match ($state) {
//                        'NORMAL' => 'gray',
//                        'FULL_TIME' => 'success',
//                        'SUPERVISAO' => 'warning',
//                        'GART' => 'danger',
//                        'ESCOLTA' => 'info',
//                        default => 'secondary',
//                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('local_ativacao')
                    ->label('Local Ativação')
                    ->searchable()
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('combustivel')
                    ->label('Combustível')
                    ->searchable()
                    ->badge()
//                    ->color(fn (string $state): string => match ($state) {
//                        'GASOLINA' => 'warning',
//                        'ETANOL' => 'success',
//                        'DIESEL' => 'danger',
//                        'FLEX' => 'info',
//                        'GNV' => 'primary',
//                        'ELETRICO' => 'success',
//                        default => 'gray',
//                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function ($state): string {
                        if (!$state instanceof StatusVeiculo) {
                            return 'gray';
                        }
                        return $state->getColor();
                    })
                    ->icon(function ($state): ?string {
                        if (!$state instanceof StatusVeiculo) {
                            return null;
                        }
                        return $state->getIcon();
                    })
                    ->iconPosition('before')
                    ->tooltip(function ($state): string {
                        if (!$state instanceof StatusVeiculo) {
                            return 'Status não definido';
                        }
                        return $state->getDescription();
                    }),

                Tables\Columns\TextColumn::make('quilometragem')
                    ->label('Quilometragem')
                    ->numeric(0)
                    ->sortable()
                    ->suffix(' Km')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('km_proxima_revisao')
                    ->label('Próxima Revisão')
                    ->numeric(0)
                    ->sortable()
                    ->suffix(' Km')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('revisao_pendente')
                    ->label('Revisão Pendente')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('possui_bateria_auxiliar')
                    ->label('Bateria Aux.')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('possui_gps')
                    ->label('GPS')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('data_recebimento')
                    ->label('Data Recebimento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data_devolucao')
                    ->label('Data Devolução')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('chassi')
                    ->label('Chassi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(10),

                Tables\Columns\TextColumn::make('renavam')
                    ->label('RENAVAM')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ano_fabricacao')
                    ->label('Ano Fab.')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ano_modelo')
                    ->label('Ano Modelo')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cor')
                    ->label('Cor')
                    ->searchable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('valor_diaria')
                    ->label('Valor Diária')
                    ->numeric(2)
                    ->money('BRL')
                    ->sortable()
                    ->alignEnd()
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
                Tables\Filters\SelectFilter::make('id_modelo')
                    ->label('Filtrar por Modelo')
                    ->relationship('modelo', 'nome_modelo')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('direcionamento')
                    ->label('Filtrar por Direcionamento')
                    ->options(DirecionamentoVeiculo::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('local_ativacao')
                    ->label('Filtrar por Local de Ativação')
                    ->options(LocalAtivacaoVeiculo::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('combustivel')
                    ->label('Filtrar por Combustível')
                    ->options(CombustivelVeiculo::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(StatusVeiculo::class)
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('revisao_pendente')
                    ->label('Revisão Pendente')
                    ->boolean()
                    ->trueLabel('Com revisão pendente')
                    ->falseLabel('Sem revisão pendente')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('possui_gps')
                    ->label('Possui GPS')
                    ->boolean()
                    ->trueLabel('Com GPS')
                    ->falseLabel('Sem GPS')
                    ->native(false),

                Tables\Filters\Filter::make('quilometragem')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('km_min')
                                    ->label('Quilometragem Mínima')
                                    ->numeric()
                                    ->placeholder('Ex: 10000'),
                                Forms\Components\TextInput::make('km_max')
                                    ->label('Quilometragem Máxima')
                                    ->numeric()
                                    ->placeholder('Ex: 100000'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['km_min'],
                                fn (Builder $query, $value): Builder => $query->where('quilometragem', '>=', $value),
                            )
                            ->when(
                                $data['km_max'],
                                fn (Builder $query, $value): Builder => $query->where('quilometragem', '<=', $value),
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
            RelationManagers\HistoricoVeiculosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVeiculos::route('/'),
            'view' => Pages\ViewVeiculo::route('/{record}'),
            'create' => Pages\CreateVeiculo::route('/create'),
            'edit' => Pages\EditVeiculo::route('/{record}/edit'),
        ];
    }
}
