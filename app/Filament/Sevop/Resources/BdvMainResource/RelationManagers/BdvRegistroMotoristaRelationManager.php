<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\RelationManagers;

use App\Enums\NivelCombustivelEnum;
use App\Enums\TipoRegistroStatusEnum;
use App\Enums\TipoTurnoEnum;
use App\Models\BdvItemStatus;
use App\Models\Pessoa;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BdvRegistroMotorista;
use Filament\Forms\Components\DateTimePicker;

class BdvRegistroMotoristaRelationManager extends RelationManager
{
    protected static string $relationship = 'registrosMotorista';
    protected static ?string $title = 'Registros de Condutores por Turno';
    protected static ?string $modelLabel = 'Registro de Turno';
    protected static ?string $pluralModelLabel = 'Registros de Turnos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Seção para o Registro de Saída
                Section::make('Informações de Saída do Turno')
                    ->description('Detalhes do condutor e do estado do veículo na saída.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('id_condutor')
                                    ->label('Condutor')
                                    ->relationship('condutor', 'nome', fn(Builder $query) => $query->where('ativo', true))
                                    ->getOptionLabelFromRecordUsing(fn(Pessoa $record) => "{$record->nome}")
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                Select::make('tipo_turno')
                                    ->label('Tipo de Turno')
                                    ->options(TipoTurnoEnum::class)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                DateTimePicker::make('momento_saida')
                                    ->label('Data e Hora da Saída')
                                    ->required()
                                    ->displayFormat('d/m/Y H:i')
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                TextInput::make('km_saida')
                                    ->label('Quilometragem na Saída')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix(' Km')
                                    ->required()
                                    ->placeholder('Ex: 12345.67')
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                Select::make('nivel_combustivel_saida')
                                    ->label('Nível de Combustível na Saída')
                                    ->options(NivelCombustivelEnum::class)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'),

                                Select::make('id_encarregado_saida')
                                    ->label('Encarregado na Saída')
                                    ->relationship('encarregadoSaida', 'nome', fn(Builder $query) => $query->where('ativo', true))
                                    ->getOptionLabelFromRecordUsing(fn(Pessoa $record) => "{$record->nome}")
//                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'),
                            ]),

                        Textarea::make('observacoes_saida')
                            ->label('Observações do Condutor na Saída')
                            ->maxLength(65535)
                            ->rows(2)
                            ->placeholder('Condições do veículo, observações do condutor, etc.')
                            ->columnSpanFull()
                            ->disabled(fn(string $operation) => $operation === 'edit'),
                    ])
                    ->collapsible()
                    ->collapsed(fn(string $operation) => $operation === 'edit'),

                // Seção de Itens de Verificação na Saída
                Section::make('Verificação de Itens do Veículo (Saída)')
                    ->description('Condição dos itens do veículo registrada na saída.')
                    ->schema($this->getItemStatusFields('saida'))
                    ->collapsible()
                    ->collapsed(fn(string $operation) => $operation === 'edit')
                    ->disabled(fn(string $operation) => $operation === 'edit'),

                // Seção para o Registro de Chegada
                Section::make('Informações de Chegada do Turno')
                    ->description('Preencha os detalhes quando o condutor retornar.')
                    ->visible(fn(string $operation) => $operation === 'edit' || $operation === 'view') // <-- MUDANÇA AQUI
                    ->schema([
                        Placeholder::make('status_chegada')
                            ->content(fn($record) => $record->momento_chegada ? 'Turno Finalizado' : 'Aguardando Chegada'),

                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('momento_chegada')
                                    ->label('Data e Hora da Chegada')
                                    ->displayFormat('d/m/Y H:i')
                                    ->columnSpan(1),

                                TextInput::make('km_chegada')
                                    ->label('Quilometragem na Chegada')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix(' Km')
                                    ->placeholder('Ex: 12450.99')
                                    ->columnSpan(1),

                                Select::make('nivel_combustivel_chegada')
                                    ->label('Nível de Combustível na Chegada')
                                    ->options(NivelCombustivelEnum::class)
                                    ->native(false)
                                    ->columnSpan(1),

                                Select::make('id_encarregado_chegada')
                                    ->label('Encarregado na Chegada')
                                    ->relationship('encarregadoChegada', 'nome', fn(Builder $query) => $query->where('ativo', true))
                                    ->getOptionLabelFromRecordUsing(fn(Pessoa $record) => "{$record->nome}")
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2),
                            ]),

                        Textarea::make('observacoes_chegada')
                            ->label('Observações do Condutor na Chegada')
                            ->maxLength(65535)
                            ->rows(2)
                            ->placeholder('Condições do veículo, observações do condutor, etc.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn(string $operation, Forms\Get $get) => $operation === 'view' && $get('momento_chegada') === null), // <-- MUDANÇA AQUI

                // Seção de Itens de Verificação na Chegada
                Section::make('Verificação de Itens do Veículo (Chegada)')
                    ->description('Marque a condição de cada item do veículo na chegada.')
                    ->visible(fn(string $operation) => $operation === 'edit' || $operation === 'view') // <-- MUDANÇA AQUI
                    ->schema($this->getItemStatusFields('chegada'))
                    ->collapsible()
                    ->collapsed(fn(string $operation, Forms\Get $get) => $operation === 'view' && $get('momento_chegada') === null), // <-- MUDANÇA AQUI
            ]);
    }

    // Resto do código da table() permanece igual...
    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['itemStatus']))
            ->recordTitleAttribute('titulo_bdv') // Título personalizado para Modais do BDV
            ->defaultSort('momento_saida', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tipo_turno')
                    ->label('Turno')
                    ->badge()
                    ->color(fn (TipoTurnoEnum $state): string => match ($state) {
                        TipoTurnoEnum::MATUTINO => 'info',
                        TipoTurnoEnum::VESPERTINO => 'warning',
                        TipoTurnoEnum::NOTURNO => 'danger',
                        TipoTurnoEnum::DIURNO => 'success',
                    }),
                Tables\Columns\TextColumn::make('condutor.nome')
                    ->label('Condutor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('momento_saida')
                    ->label('Saída')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('momento_chegada')
                    ->label('Chegada')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Aguardando...')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas_em_atuacao')
                    ->label('Hrs em Atuação')
                    ->formatStateUsing(function (?float $state): string {
                        if ($state === null) {
                            return 'N/A';
                        }

                        return number_format($state, 1) . 'h';
                    })
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 24 => 'danger',
                        $state >= 12 => 'warning',
                        $state >= 8 => 'success',
                        default => 'primary'
                    })
                    ->tooltip(function ($record): ?string {
                        if ($record->momento_saida && $record->momento_chegada) {
                            $saida = Carbon::parse($record->momento_saida)->format('d/m/Y H:i');
                            $chegada = Carbon::parse($record->momento_chegada)->format('d/m/Y H:i');
                            return "Saída: {$saida}\nChegada: {$chegada}";
                        }
                        return 'Momentos não informados';
                    }),
                Tables\Columns\TextColumn::make('km_saida')
                    ->label('KM Saída')
                    ->numeric(0)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->suffix(' Km'),
                Tables\Columns\TextColumn::make('km_chegada')
                    ->label('KM Chegada')
                    ->numeric(0)
                    ->suffix(' Km')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('quilometragem_rodada')
                    ->label('KM Rodada')
                    ->numeric(0)
                    ->suffix(' Km')
                    ->placeholder('N/A')
                    ->state(function ($record): ?float {
                        // Calcula a diferença apenas se ambos os valores existirem
                        if ($record->km_saida !== null && $record->km_chegada !== null) {
                            // Garante que o cálculo seja preciso, arredondando para duas casas decimais
                            return round($record->km_chegada - $record->km_saida, 2);
                        }
                        return null; // Retorna nulo se não puder calcular
                    })
                    ->tooltip(function ($record): ?string {
                        if ($record->km_saida && $record->km_chegada) {
                            $saida = $record->km_saida;
                            $chegada = $record->km_chegada;
                            return "Saída: {$saida}\nChegada: {$chegada}";
                        }
                        return 'Km´s não informados';
                    }),
                Tables\Columns\TextColumn::make('status_turno')
                    ->label('Status')
                    ->badge()
                    ->state(function ($record): string {
                        return $record->momento_chegada ? 'Finalizado' : 'Em Andamento';
                    })
                    ->color(function ($record): string {
                        return $record->momento_chegada ? 'success' : 'warning';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_turno')
                    ->label('Filtrar por Turno')
                    ->options(TipoTurnoEnum::class)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('id_condutor')
                    ->label('Filtrar por Condutor')
                    ->relationship('condutor', 'nome')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('turno_em_andamento')
                    ->label('Turnos em Andamento')
                    ->query(fn (Builder $query): Builder => $query->whereNull('momento_chegada'))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Novo Turno')
                    ->modalWidth('7xl')
                    ->slideOver()
                    ->using(function (array $data): BdvRegistroMotorista {
                        // Este método substitui completamente o processo de criação do Filament
                        return $this->createRegistroMotorista($data);
                    })
                    ->successNotificationTitle('Turno registrado com sucesso!'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make() // Esta é a linha a ser modificada
                ->modalWidth('7xl')
                    ->fillForm(function ($record) { // Adicione esta linha
                        return $this->fillEditForm($record); // Adicione esta linha
                    }), // Adicione esta linha
                Tables\Actions\EditAction::make()
                    ->label('Finalizar Turno / Editar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->modalWidth('7xl')
                    ->slideOver()
                    ->fillForm(function ($record) {
                        return $this->fillEditForm($record);
                    })
                    ->using(function ($record, array $data): BdvRegistroMotorista {
                        // Este método substitui completamente o processo de edição do Filament
                        return $this->updateRegistroMotorista($record, $data);
                    })
                    ->successNotificationTitle(fn ($record) => $record->momento_chegada ? 'Turno finalizado com sucesso!' : 'Registro atualizado com sucesso!'),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhum registro de condutor para este BDV')
            ->emptyStateDescription('Adicione o primeiro turno para este Boletim Diário de Veículo.');
    }

    /**
     * Passo 2: Método não-estático para gerar campos de status
     */
    protected function getItemStatusFields(string $prefix): array
    {
        $numeroRodasVeiculo = $this->ownerRecord->veiculo->modelo->numero_rodas ?? 0;

        $getBooleanToggle = function (string $name, string $label) use ($prefix) {
            return Toggle::make("{$prefix}_status.{$name}")
                ->label($label)
                ->default(true);
        };

        $commonFields = [
            $getBooleanToggle('crlv', 'CRLV'),
            $getBooleanToggle('lacre_placa', 'Lacre Placa'),
            $getBooleanToggle('oleo_freio', 'Óleo Freio'),
            $getBooleanToggle('oleo_motor', 'Óleo Motor'),
            $getBooleanToggle('pneus_estado', 'Pneus Estado'),
            $getBooleanToggle('retrovisor_direito_esquerdo', 'Retrovisores D/E'),
            $getBooleanToggle('buzina', 'Buzina'),
            $getBooleanToggle('luzes_farol_alto_baixo_estacionamento', 'Luzes Farol (A/B/Estac.)'),
            $getBooleanToggle('luzes_pisca_re_freios', 'Luzes Pisca/Freios(Ré se 4 rodas)'),
            $getBooleanToggle('chaparia_pintura', 'Chaparia/Pintura'),
            $getBooleanToggle('giroflex', 'Giroflex'),
            $getBooleanToggle('sirene', 'Sirene'),
        ];

        $twoWheelFields = [
            $getBooleanToggle('velocimetro', 'Velocímetro'),
            $getBooleanToggle('bancos_estado', 'Bancos Estado'),
            $getBooleanToggle('bateria_agua', 'Bateria/Água'),
            $getBooleanToggle('paralamas_dianteiro_traseiro', 'Paralamas D/T'),
            $getBooleanToggle('descarga_completa', 'Descarga Completa'),
            $getBooleanToggle('etiqueta_revisao', 'Etiqueta Revisão'),
            $getBooleanToggle('tampas_laterais', 'Tampas Laterais'),
            $getBooleanToggle('protetor_perna', 'Protetor Perna'),
            $getBooleanToggle('fechadura_chave', 'Fechadura/Chave'),
            $getBooleanToggle('carenagem_tanque', 'Carenagem Tanque'),
            $getBooleanToggle('carenagem_farol', 'Carenagem Farol'),
            $getBooleanToggle('tanque_estrutura', 'Tanque Estrutura'),
            $getBooleanToggle('caixa_lado_esq_lado_dir', 'Caixa Lado E/D'),
            $getBooleanToggle('punhos_manete', 'Punhos/Manete'),
        ];

        $fourWheelFields = [
            $getBooleanToggle('macaco', 'Macaco'),
            $getBooleanToggle('chave_roda', 'Chave de Roda'),
            $getBooleanToggle('triangulo', 'Triângulo'),
            $getBooleanToggle('estepe', 'Estepe'),
            $getBooleanToggle('extintor', 'Extintor'),
            $getBooleanToggle('agua_radiador', 'Água Radiador'),
            $getBooleanToggle('calotas', 'Calotas'),
            $getBooleanToggle('retrovisor_interno', 'Retrovisor Interno'),
            $getBooleanToggle('macanetas_fechaduras', 'Maçanetas/Fechaduras'),
            $getBooleanToggle('limpadores', 'Limpadores'),
            $getBooleanToggle('luzes_internas', 'Luzes Internas'),
            $getBooleanToggle('cinto_seguranca', 'Cinto de Segurança'),
            $getBooleanToggle('radio_am_fm', 'Rádio AM/FM'),
            $getBooleanToggle('estofamento', 'Estofamento'),
        ];

        $schema = [
            Forms\Components\Fieldset::make("Itens Comuns (2 e 4 Rodas) - {$prefix}")
                ->schema([Grid::make(4)->schema($commonFields)]),
        ];

        if ($numeroRodasVeiculo == 2) {
            $schema[] = Forms\Components\Fieldset::make("Itens Específicos para 2 Rodas - {$prefix}")
                ->schema([Grid::make(4)->schema($twoWheelFields)]);
        } elseif ($numeroRodasVeiculo == 4) {
            $schema[] = Forms\Components\Fieldset::make("Itens Específicos para 4 Rodas - {$prefix}")
                ->schema([Grid::make(4)->schema($fourWheelFields)]);
        }

        return $schema;
    }

    /**
     * Passo 3: Método para preencher formulário de edição
     */
    protected function fillEditForm($record): array
    {
        $data = $record->toArray();

        // 1. Carregar dados de status de SAÍDA (se existirem)
        $saidaStatus = $record->itemStatus()
            ->where('tipo_registro', TipoRegistroStatusEnum::SAIDA)
            ->first();

        if ($saidaStatus) {
            foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                $data['saida_status'][$field] = (bool) $saidaStatus->{$field};
            }
        } else {
            // Fallback: Se não houver status de SAÍDA (o que seria incomum para um registro existente),
            // inicializa com true para evitar erros e garantir que os campos existam.
            foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                $data['saida_status'][$field] = true;
            }
        }

        // 2. Carregar dados de status de CHEGADA
        $chegadaStatus = $record->itemStatus()
            ->where('tipo_registro', TipoRegistroStatusEnum::CHEGADA)
            ->first();

        if ($chegadaStatus) {
            // Caso A: Já existe um registro de chegada.
            // Carrega os valores salvos do status de chegada.
            foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                $data['chegada_status'][$field] = (bool) $chegadaStatus->{$field};
            }
        } else {
            // Caso B: Não existe registro de chegada (turno sendo finalizado pela primeira vez).
            // Preenche os campos de chegada com os valores de status de SAÍDA como padrão.
            // Isso garante que os toggles de chegada reflitam o estado de saída,
            // facilitando a finalização do turno.
            if ($saidaStatus) { // Verifica se temos dados de saída para usar como base
                foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                    $data['chegada_status'][$field] = (bool) $saidaStatus->{$field};
                }
            } else {
                // Fallback: Se nem status de saída for encontrado, inicializa tudo como verdadeiro.
                foreach (BdvItemStatus::BOOLEAN_FIELDS as $field) {
                    $data['chegada_status'][$field] = true;
                }
            }
        }

        return $data;
    }

    /**
     * Passo 5: Método para processar dados de edição
     */
    protected function processEditData(array $data): array
    {
        $data['atualizado_por'] = Auth::id();

        $itemStatusChegadaData = $data['chegada_status'] ?? [];
        unset($data['chegada_status']);
        unset($data['saida_status']); // Remove dados de saída que não devem ser editados

        $registroMotorista = $this->getMountedTableActionRecord();

        if (isset($data['momento_chegada']) && $data['momento_chegada'] !== null) {
            DB::beginTransaction();
            try {
                $registroMotorista->update($data);

                // Processar status de chegada
                $existingChegadaStatus = BdvItemStatus::where('id_registro_motorista', $registroMotorista->id_registro_motorista)
                    ->where('tipo_registro', TipoRegistroStatusEnum::CHEGADA)
                    ->first();

                $itemStatusChegadaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
                $itemStatusChegadaData['tipo_registro'] = TipoRegistroStatusEnum::CHEGADA;
                $itemStatusChegadaData['cadastrado_por'] = $existingChegadaStatus ? $existingChegadaStatus->cadastrado_por : Auth::id();
                $itemStatusChegadaData['atualizado_por'] = Auth::id();

                if ($existingChegadaStatus) {
                    $existingChegadaStatus->update($itemStatusChegadaData);
                } else {
                    BdvItemStatus::create($itemStatusChegadaData);
                }

                DB::commit();
                return $registroMotorista->toArray();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return $data;
    }

    /**
     * Método dedicado para criar registro de motorista com validação de duplicatas
     */
    protected function createRegistroMotorista(array $data): BdvRegistroMotorista
    {
        // Preparar dados básicos
        $data['id_bdv'] = $this->ownerRecord->id_bdv;
        $data['cadastrado_por'] = Auth::id();
        $data['atualizado_por'] = Auth::id();

        // Extrair dados de status de saída
        $itemStatusSaidaData = $data['saida_status'] ?? [];
        unset($data['saida_status']);

        // Verificar se já existe um registro com a mesma combinação
        $existingRecord = BdvRegistroMotorista::where('id_bdv', $data['id_bdv'])
            ->where('id_condutor', $data['id_condutor'])
            ->where('tipo_turno', $data['tipo_turno'])
            ->first();

        if ($existingRecord) {
            throw new \Exception(
                "Já existe um registro para este condutor no turno {$data['tipo_turno']->getLabel()} neste BDV. " .
                "Cada condutor pode ter apenas um turno de cada tipo por BDV."
            );
        }

        DB::beginTransaction();
        try {
            // Criar o registro de motorista
            $registroMotorista = BdvRegistroMotorista::create($data);

            // Criar BdvItemStatus para Saída
            if (!empty($itemStatusSaidaData)) {
                $itemStatusSaidaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
                $itemStatusSaidaData['tipo_registro'] = TipoRegistroStatusEnum::SAIDA;
                $itemStatusSaidaData['cadastrado_por'] = Auth::id();
                $itemStatusSaidaData['atualizado_por'] = Auth::id();
                BdvItemStatus::create($itemStatusSaidaData);
            }

            DB::commit();
            return $registroMotorista;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Método dedicado para atualizar registro de motorista
     */
    protected function updateRegistroMotorista(BdvRegistroMotorista $record, array $data): BdvRegistroMotorista
    {
        $data['atualizado_por'] = Auth::id();

        // Extrair dados de status de chegada
        $itemStatusChegadaData = $data['chegada_status'] ?? [];
        unset($data['chegada_status']);
        unset($data['saida_status']); // Remove dados de saída que não devem ser editados

        DB::beginTransaction();
        try {
            // Atualizar o registro de motorista
            $record->update($data);

            // Processar status de chegada se fornecido
            if (!empty($itemStatusChegadaData) && isset($data['momento_chegada']) && $data['momento_chegada'] !== null) {
                $existingChegadaStatus = BdvItemStatus::where('id_registro_motorista', $record->id_registro_motorista)
                    ->where('tipo_registro', TipoRegistroStatusEnum::CHEGADA)
                    ->first();

                $itemStatusChegadaData['id_registro_motorista'] = $record->id_registro_motorista;
                $itemStatusChegadaData['tipo_registro'] = TipoRegistroStatusEnum::CHEGADA;
                $itemStatusChegadaData['cadastrado_por'] = $existingChegadaStatus ? $existingChegadaStatus->cadastrado_por : Auth::id();
                $itemStatusChegadaData['atualizado_por'] = Auth::id();

                if ($existingChegadaStatus) {
                    $existingChegadaStatus->update($itemStatusChegadaData);
                } else {
                    BdvItemStatus::create($itemStatusChegadaData);
                }
            }

            DB::commit();
            return $record->fresh(); // Retorna o registro atualizado

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
