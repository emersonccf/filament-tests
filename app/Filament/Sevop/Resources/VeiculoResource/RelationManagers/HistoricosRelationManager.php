<?php

namespace App\Filament\Sevop\Resources\VeiculoResource\RelationManagers;

use App\Enums\DirecionamentoVeiculo;
use App\Enums\PrioridadeHistorico;
use App\Enums\StatusEventoHistorico;
use App\Enums\TipoEventoHistorico;
use App\Models\HistoricoVeiculo;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Auth;

class HistoricoVeiculosRelationManager extends RelationManager
{
    protected static string $relationship = 'historicoVeiculos';

    protected static ?string $title = 'Histórico do Veículo';

    protected static ?string $modelLabel = 'Evento';

    protected static ?string $pluralModelLabel = 'Eventos';

    protected static ?string $recordTitleAttribute = 'tipo_evento';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Seção para criação completa de novo evento
                Section::make('Identificação do Evento')
                    ->description('Dados básicos do evento')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Campo oculto para o veículo (já definido pelo relacionamento)
                                Hidden::make('id_veiculo')
                                    ->default(fn () => $this->ownerRecord->id_veiculo),

                                // Exibe o veículo atual (não editável)
                                Placeholder::make('veiculo_atual')
                                    ->label('Veículo')
                                    ->content(fn () => $this->ownerRecord->placa_modelo_direcionamento)
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
                    ->collapsible()
                    ->visible(fn (string $operation): bool => $operation === 'create'),

                Section::make('Detalhes do Evento')
                    ->description('Informações específicas sobre o evento')
                    ->schema([
                        Grid::make(3)
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
                                    ->default(fn () => $this->ownerRecord->quilometragem ?? null)
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
                    ->collapsible()
                    ->visible(fn (string $operation): bool => $operation === 'create'),

                Section::make('Localização e Responsáveis')
                    ->description('Local do evento e prestadores de serviço')
                    ->schema([
                        Grid::make(2)
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
                    ->collapsible()
                    ->visible(fn (string $operation): bool => $operation === 'create'),

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
                    ->collapsible()
                    ->visible(fn (string $operation): bool => $operation === 'create'),

                Section::make('Descrição do Evento')
                    ->description('Detalhamento do evento')
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
                    ->collapsible()
                    ->visible(fn (string $operation): bool => $operation === 'create'),

                // Seção específica para acompanhamento (visível sempre, mas com foco na edição)
                Section::make('Status e Controle de Acompanhamento da Ocorrência')
                    ->description(fn (string $operation): string =>
                    $operation === 'edit'
                        ? 'Atualize o status e controle do acompanhamento da ocorrência'
                        : 'Status inicial do evento e controle de acompanhamento'
                    )
                    ->schema([
                        // Informações do evento (somente leitura na edição)
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('evento_info')
                                    ->label('Evento')
                                    ->content(function (string $operation, $record): string {
                                        if ($operation === 'edit' && $record) {
                                            return "{$record->tipo_evento->getLabel()} - {$record->data_evento->format('d/m/Y')}";
                                        }
                                        return 'Novo evento';
                                    })
                                    ->columnSpan(1)
                                    ->visible(fn (string $operation): bool => $operation === 'edit'),

                                Placeholder::make('veiculo_info')
                                    ->label('Veículo')
                                    ->content(fn () => $this->ownerRecord->placa_modelo_direcionamento)
                                    ->columnSpan(1)
                                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                            ])
                            ->visible(fn (string $operation): bool => $operation === 'edit'),

                        // Campos de controle (sempre visíveis)
                        Grid::make(4)
                            ->schema([
                                Select::make('status_evento')
                                    ->label('Status do Evento')
                                    ->options(StatusEventoHistorico::class)
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->helperText('Situação atual do evento')
                                    ->columnSpan(1)
                                    ->default(fn (string $operation): ?string =>
                                    $operation === 'create' ? StatusEventoHistorico::PENDENTE->value : null
                                    ),

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
                                    ->helperText('Informações complementares sobre o acompanhamento')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(false), // Sempre expandida para facilitar o acesso

                // Campos de auditoria (ocultos)
                Hidden::make('cadastrado_por')
                    ->default(fn () => Auth::id()),
                Hidden::make('atualizado_por')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo_evento')
            ->defaultSort('data_evento', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tipo_evento')
                    ->label('Tipo')
                    ->badge(),

                Tables\Columns\TextColumn::make('data_evento')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_evento')
                    ->label('Hora')
                    ->time('H:i')
                    ->placeholder('--:--'),

                Tables\Columns\TextColumn::make('prioridade')
                    ->label('Prioridade')
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
                    ->iconPosition('before'),

                Tables\Columns\TextColumn::make('status_evento')
                    ->label('Status')
                    ->badge()
                    ->color(fn (StatusEventoHistorico $state): string => $state->getColor())
                    ->icon(fn (StatusEventoHistorico $state): string => $state->getIcon())
                    ->iconPosition('before'),

                Tables\Columns\IconColumn::make('teve_vitima')
                    ->label('Vítima')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('afeta_disponibilidade')
                    ->label('Afeta Disp.')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('pessoa.nome')
                    ->label('Cond. Envolvido')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nenhum')
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('quilometragem')
                    ->label('Km')
                    ->numeric(0)
                    ->suffix(' Km')
                    ->alignEnd()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('veiculoSubstituto.placa')
                    ->label('Substituto')
                    ->badge()
                    ->color('warning')
                    ->placeholder('Nenhum'),

                Tables\Columns\TextColumn::make('data_prevista_conclusao')
                    ->label('Prev. Conclusão')
                    ->date('d/m/Y')
                    ->badge()
                    ->color('info')
                    ->placeholder('Não definida'),

                Tables\Columns\TextColumn::make('data_conclusao')
                    ->label('Conclusão')
                    ->date('d/m/Y')
                    ->badge()
                    ->color('success')
                    ->placeholder('Pendente'),

                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_evento')
                    ->label('Tipo de Evento')
                    ->options(TipoEventoHistorico::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status_evento')
                    ->label('Status')
                    ->options(StatusEventoHistorico::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('prioridade')
                    ->label('Prioridade')
                    ->options(PrioridadeHistorico::class)
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('afeta_disponibilidade')
                    ->label('Afeta Disponibilidade')
                    ->boolean()
                    ->trueLabel('Afeta')
                    ->falseLabel('Não afeta'),

                Tables\Filters\Filter::make('eventos_pendentes')
                    ->label('Eventos Pendentes')
                    ->query(fn (Builder $query): Builder =>
                    $query->whereIn('status_evento', [StatusEventoHistorico::PENDENTE->value, StatusEventoHistorico::EM_ANDAMENTO->value ])
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('data_evento')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('data_from')
                                    ->label('Data de')
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\DatePicker::make('data_until')
                                    ->label('Data até')
                                    ->displayFormat('d/m/Y'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_evento', '>=', $date),
                            )
                            ->when(
                                $data['data_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_evento', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Evento')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Registrar Novo Evento')
                    ->modalWidth('7xl')
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['cadastrado_por'] = Auth::id();
                        $data['atualizado_por'] = Auth::id();
                        return $data;
                    })
                    ->successNotificationTitle('Evento registrado com sucesso!')
                    ->after(function () {
                        // Opcional: Atualizar status do veículo se necessário
                        // $this->ownerRecord->refresh();
                    }),
            ])
            ->actions([
//                Tables\Actions\ViewAction::make()
//                    ->label('Visualizar')
//                    ->modalWidth('6xl')
//                    ->slideOver(),

                Tables\Actions\EditAction::make()
                    ->label('Acompanhar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->modalHeading(fn ($record) => "Acompanhamento - {$record->tipo_evento->getLabel()}")
                    ->modalWidth('6xl')
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['atualizado_por'] = Auth::id();
                        return $data;
                    })
                    ->successNotificationTitle('Acompanhamento atualizado com sucesso!'),

//                Tables\Actions\DeleteAction::make()
//                    ->requiresConfirmation()
//                    ->modalHeading('Excluir Evento')
//                    ->modalDescription('Tem certeza que deseja excluir este evento? Esta ação não pode ser desfeita.')
//                    ->successNotificationTitle('Evento excluído com sucesso!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Nenhum evento registrado')
            ->emptyStateDescription('Este veículo ainda não possui eventos registrados em seu histórico.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->striped()
            ->paginationPageOptions([5, 10, 20, 50, 100, 'all'])
            ->defaultPaginationPageOption(5);
    }

    /**
     * Customiza o comportamento após criar um registro
     */
    protected function afterCreate(): void
    {
        // Opcional: Lógica adicional após criar um evento
        // Por exemplo, notificar supervisores, atualizar status do veículo, etc.
    }

    /**
     * Customiza o comportamento após editar um registro
     */
    protected function afterSave(): void
    {
        // Opcional: Lógica adicional após salvar um evento
        // Por exemplo, verificar se o evento foi concluído e atualizar status do veículo
    }
}
