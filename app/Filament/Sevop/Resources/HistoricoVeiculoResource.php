<?php

namespace App\Filament\Sevop\Resources;

use App\Enums\DirecionamentoVeiculo;
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
use Filament\Forms\Components\TextInput;
use App\Models\Veiculo;
use App\Models\User;
use App\Enums\TipoEventoHistorico;
use App\Enums\PrioridadeHistorico;
use App\Enums\StatusEventoHistorico;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;

class HistoricoVeiculoResource extends Resource
{
    protected static ?string $model = HistoricoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Controle de Frota';
    protected static ?int $navigationSort = 60;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identificação do Evento')
                    ->description('Dados básicos do evento e veículo envolvido')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Select::make('id_veiculo')
                                    ->label('Veículo')
                                    ->relationship(
                                        name: 'veiculo',
                                        titleAttribute: 'placa',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->with(['modelo', 'modelo.marca'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => $record->placa_modelo_direcionamento)
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Selecione o veículo envolvido no evento')
                                    ->columnSpan(1),

                                Select::make('tipo_evento')
                                    ->label('Tipo de Evento')
                                    ->options(TipoEventoHistorico::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->helperText('Categoria do evento ocorrido')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Detalhes do Evento')
                    ->description('Informações específicas sobre o evento')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                DatePicker::make('data_evento')
                                    ->label('Data do Evento')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->helperText('Data em que o evento ocorreu')
                                    ->columnSpan(1),

                                TimePicker::make('hora_evento')
                                    ->label('Hora do Evento')
                                    ->displayFormat('H:i')
                                    ->helperText('Horário aproximado do evento')
                                    ->columnSpan(1),

                                TextInput::make('quilometragem')
                                    ->label('Quilometragem')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('Km')
                                    ->placeholder('Ex: 45000.5')
                                    ->helperText('Quilometragem no momento do evento')
                                    ->columnSpan(1),

                                Select::make('prioridade')
                                    ->label('Prioridade')
                                    ->options(PrioridadeHistorico::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->helperText('Nível de prioridade do evento')
                                    ->columnSpan(1),

                                Toggle::make('teve_vitima')
                                    ->label('Teve Vítima?')
                                    ->inline(false)
                                    ->helperText('Marque se houve vítimas no evento')
                                    ->columnSpan(1),

                                Toggle::make('afeta_disponibilidade')
                                    ->label('Afeta Disponibilidade?')
                                    ->inline(false)
                                    ->helperText('Marque se o veículo ficou indisponível')
                                    ->columnSpan(1),

                                Select::make('id_pessoa')
                                    ->label('Condutor Envolvido')
                                    ->relationship(
                                        name: 'pessoa',
                                        titleAttribute: 'nome',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Selecione o nome do condutor envolvido')
                                    ->columnSpan(3),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Localização e Responsáveis')
                    ->description('Local do evento e prestadores de serviço')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('local_ocorrencia')
                                    ->label('Local da Ocorrência')
                                    ->maxLength(200)
                                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                                    $state ? mb_strtoupper($state) : null
                                    )
                                    ->placeholder('RUA EXEMPLO, 123 - BAIRRO')
                                    ->helperText('Endereço onde ocorreu o evento')
                                    ->columnSpan(1),

                                TextInput::make('prestador_servico')
                                    ->label('Prestador de Serviço')
                                    ->maxLength(100)
                                    ->dehydrateStateUsing(fn (?string $state): ?string =>
                                    $state ? mb_strtoupper($state) : null
                                    )
                                    ->placeholder('OFICINA EXEMPLO LTDA')
                                    ->helperText('Empresa responsável pelo atendimento')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Veículo Substituto')
                    ->description('Veículo de reserva utilizado durante o evento')
                    ->schema([
                        Select::make('id_veiculo_substituto')
                            ->label('Veículo Substituto')
                            ->relationship(
                                name: 'veiculoSubstituto',
                                titleAttribute: 'placa',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('direcionamento', DirecionamentoVeiculo::RESERVA)
                                    ->where('status', 'ATIVO')
                                    ->with(['modelo', 'modelo.marca'])
                            )
                            ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => $record->placa_modelo_direcionamento)
                            ->searchable()
                            ->preload()
                            ->helperText('Selecione apenas veículos de reserva ativos')
                            ->placeholder('Nenhum veículo substituto'),
                    ])
                    ->collapsible(),

                Section::make('Descrição e Observações')
                    ->description('Detalhamento do evento e observações adicionais')
                    ->schema([
                        Textarea::make('descricao')
                            ->label('Descrição do Evento')
                            ->required()
                            ->maxLength(65535)
                            ->rows(4)
                            ->placeholder('Descreva detalhadamente o que aconteceu...')
                            ->helperText('Descrição obrigatória do evento')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Status e Controle de Acompanhamento da Ocorrência')
                    ->description('Status do evento e datas de controle')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Select::make('status_evento')
                                    ->label('Status do Evento')
                                    ->options(StatusEventoHistorico::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->helperText('Situação atual do evento')
                                    ->columnSpan(1),

                                DatePicker::make('data_prevista_conclusao')
                                    ->label('Data Prevista para Conclusão')
                                    ->displayFormat('d/m/Y')
                                    ->helperText('Previsão de resolução do evento')
                                    ->columnSpan(1),

                                DatePicker::make('data_conclusao')
                                    ->label('Data de Conclusão')
                                    ->displayFormat('d/m/Y')
                                    ->helperText('Data efetiva de resolução')
                                    ->columnSpan(1),

                                TimePicker::make('hora_conclusao')
                                    ->label('Hora de Retorno do Veículo')
                                    ->displayFormat('H:i')
                                    ->helperText('Horário de retorno à operação')
                                    ->columnSpan(1),

                                Textarea::make('observacoes')
                                    ->label('Observações Adicionais Finais Após Atendimento da Ocorrência')
                                    ->maxLength(65535)
                                    ->rows(3)
                                    ->placeholder('Informações complementares, providências tomadas, etc...')
                                    ->helperText('Informações complementares')
                                    ->columnSpanFull(),
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
                                        ->content(function (string $operation, ?HistoricoVeiculo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userCreatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('created_at_display')
                                        ->label('Data de Criação')
                                        ->content(function (string $operation, ?HistoricoVeiculo $record): string {
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
                                        ->content(function (string $operation, ?HistoricoVeiculo $record): string {
                                            if ($operation === 'create') {
                                                return Auth::user()->name;
                                            }
                                            return $record?->userUpdatedBy?->name ?? 'N/A';
                                        }),

                                    Placeholder::make('updated_at_display')
                                        ->label('Última Atualização')
                                        ->content(function (string $operation, ?HistoricoVeiculo $record): string {
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
            ->defaultSort('data_evento', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa_modelo_direcionamento')
                    ->label('Veículo')
//                    ->searchable(['veiculo.placa', 'veiculo.prefixo_veiculo'])
//                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Placa copiada!')
                    ->copyMessageDuration(1500)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tipo_evento')
                    ->label('Tipo de Evento')
                    ->searchable()
//                    ->color(fn (string $state): string => match ($state) {
//                        'ACIDENTE' => 'danger',
//                        'MANUTENCAO' => 'warning',
//                        'REVISAO' => 'info',
//                        'ABASTECIMENTO' => 'success',
//                        'MULTA' => 'danger',
//                        'SINISTRO' => 'danger',
//                        default => 'gray',
//                    })
                    ->badge(),

                Tables\Columns\TextColumn::make('data_evento')
                    ->label('Data do Evento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color('secondary'),

                Tables\Columns\TextColumn::make('hora_evento')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('prioridade')
                    ->label('Prioridade')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function ($state): string {
                        if (!$state instanceof PrioridadeHistorico) {
                            return 'gray';
                        }
                        return $state->getColor();
                    })
                    ->icon(function ($state): ?string {
                        if (!$state instanceof PrioridadeHistorico) {
                            return null;
                        }
                        return $state->getIcon();
                    })
                    ->iconPosition('before')
                    ->tooltip(function ($state): string {
                        if (!$state instanceof PrioridadeHistorico) {
                            return 'Prioridade não definida';
                        }
                        return $state->getDescription();
                    }),

                Tables\Columns\TextColumn::make('status_evento')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn (StatusEventoHistorico $state): string => $state->getColor())
                    ->icon(fn (StatusEventoHistorico $state): string => $state->getIcon())
                    ->iconPosition('before')
                    ->tooltip(fn (StatusEventoHistorico $state): string => $state->getDescription() ?? "Status: {$state->getLabel()}"),

                Tables\Columns\IconColumn::make('teve_vitima')
                    ->label('Vítima')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->alignCenter()
                    ->tooltip(fn ($state) => $state ? 'Evento com vítimas' : 'Evento sem vítimas'),

                Tables\Columns\IconColumn::make('afeta_disponibilidade')
                    ->label('Afeta Disp.')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->alignCenter()
                    ->tooltip(fn ($state) => $state ? 'Afeta disponibilidade' : 'Não afeta disponibilidade'),

                Tables\Columns\TextColumn::make('pessoa.nome')
                    ->label('Cond. Envolvido')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nenhum')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\TextColumn::make('quilometragem')
                    ->label('Km')
                    ->numeric(0)
                    ->sortable()
                    ->suffix(' Km')
                    ->alignEnd()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('veiculoSubstituto.placa')
                    ->label('Substituto')
                    ->badge()
                    ->color('warning')
                    ->placeholder('Nenhum')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('local_ocorrencia')
                    ->label('Local')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('prestador_servico')
                    ->label('Prestador')
                    ->searchable()
                    ->limit(25)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data_prevista_conclusao')
                    ->label('Prev. Conclusão')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data_conclusao')
                    ->label('Conclusão')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('hora_conclusao')
                    ->label('Hora Retorno')
                    ->time('H:i')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->wrap()
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('observacoes')
                    ->label('Observações')
                    ->wrap()
                    ->limit(40)
                    ->searchable()
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Cadastro')
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

                Tables\Filters\SelectFilter::make('tipo_evento')
                    ->label('Filtrar por Tipo de Evento')
                    ->options(TipoEventoHistorico::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('prioridade')
                    ->label('Filtrar por Prioridade')
                    ->options(PrioridadeHistorico::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status_evento')
                    ->label('Filtrar por Status')
                    ->options(StatusEventoHistorico::class)
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('teve_vitima')
                    ->label('Teve Vítima')
                    ->boolean()
                    ->trueLabel('Com vítimas')
                    ->falseLabel('Sem vítimas')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('afeta_disponibilidade')
                    ->label('Afeta Disponibilidade')
                    ->boolean()
                    ->trueLabel('Afeta disponibilidade')
                    ->falseLabel('Não afeta disponibilidade')
                    ->native(false),

                Tables\Filters\Filter::make('data_evento')
                    ->label('Período do Evento')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('data_evento_from')
                                    ->label('Data do Evento (De)')
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\DatePicker::make('data_evento_until')
                                    ->label('Data do Evento (Até)')
                                    ->displayFormat('d/m/Y'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_evento_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_evento', '>=', $date),
                            )
                            ->when(
                                $data['data_evento_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_evento', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('quilometragem')
                    ->label('Quilometragem')
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

                Tables\Filters\Filter::make('eventos_pendentes')
                    ->label('Eventos Pendentes')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status_evento', ['ABERTO', 'EM_ANDAMENTO']))
                    ->toggle(),

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
            'index' => Pages\ListHistoricoVeiculos::route('/'),
            'create' => Pages\CreateHistoricoVeiculo::route('/create'),
            'edit' => Pages\EditHistoricoVeiculo::route('/{record}/edit'),
        ];
    }
}
