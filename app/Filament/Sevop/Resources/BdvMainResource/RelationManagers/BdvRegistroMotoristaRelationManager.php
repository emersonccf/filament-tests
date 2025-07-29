<?php

namespace App\Filament\Sevop\Resources\BdvMainResource\RelationManagers;

use App\Enums\NivelCombustivelEnum;
use App\Enums\TipoRegistroStatusEnum;
use App\Enums\TipoTurnoEnum;
use App\Models\BdvItemStatus;
use App\Models\Pessoa;
use App\Models\Veiculo; // Necessário para obter numero_rodas do veículo pai
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DataTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BdvRegistroMotorista;
use Filament\Forms\Components\DateTimePicker; // Corrigido de DataTimePicker



class BdvRegistroMotoristaRelationManager extends RelationManager
{
    protected static string $relationship = 'registrosMotorista';
    protected static ?string $title = 'Registros de Condutores por Turno';
    protected static ?string $modelLabel = 'Registro de Turno';
    protected static ?string $pluralModelLabel = 'Registros de Turnos';

    public function form(Form $form): Form
    {
        // Obtém o número de rodas do veículo pai para a lógica de visibilidade
        $numeroRodasVeiculo = $this->ownerRecord->veiculo->numero_rodas ?? 0;

        return $form
            ->schema([
                // Seção para o Registro de Saída (para criação de novo turno ou visualização/edição)
                Section::make('Informações de Saída do Turno')
                    ->description('Detalhes do condutor e do estado do veículo na saída.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('id_condutor')
                                    ->label('Condutor')
                                    ->relationship('condutor', 'nome', fn(Builder $query) => $query->where('ativo', true))
                                    ->getOptionLabelFromRecordUsing(fn(Pessoa $record) => "{$record->nome}") // TODO: pode retirar
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar condutor ao editar
                                Select::make('tipo_turno')
                                    ->label('Tipo de Turno')
                                    ->options(TipoTurnoEnum::class)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar tipo de turno ao editar
                                Forms\Components\DateTimePicker::make('momento_saida')
                                    ->label('Data e Hora da Saída')
                                    ->required()
                                    ->displayFormat('d/m/Y H:i')
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar momento de saída ao editar
                                TextInput::make('km_saida')
                                    ->label('Quilometragem na Saída')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix(' Km')
                                    ->required()
                                    ->placeholder('Ex: 12345.67')
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar KM de saída ao editar
                                Select::make('nivel_combustivel_saida')
                                    ->label('Nível de Combustível na Saída')
                                    ->options(NivelCombustivelEnum::class)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar nível de combustível ao editar
                                Select::make('id_encarregado_saida')
                                    ->label('Encarregado na Saída')
                                    ->relationship('encarregadoSaida', 'nome', fn(Builder $query) => $query->where('ativo', true))
                                    ->getOptionLabelFromRecordUsing(fn(Pessoa $record) => "{$record->nome}") // TODO: pode retirar
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(1)
                                    ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar encarregado de saída ao editar
                            ]),
                        Textarea::make('observacoes_saida')
                            ->label('Observações do Condutor na Saída')
                            ->maxLength(65535)
                            ->rows(2)
                            ->placeholder('Condições do veículo, observações do condutor, etc.')
                            ->columnSpanFull()
                            ->disabled(fn(string $operation) => $operation === 'edit'), // Não pode mudar observações de saída ao editar
                    ])
                    ->collapsible()
                    ->collapsed(fn(string $operation) => $operation === 'edit'), // Colapsa na edição para focar na chegada

                // Seção de Itens de Verificação na Saída (apenas visualização na edição, editável na criação)
                Section::make('Verificação de Itens do Veículo (Saída)')
                    ->description('Condição dos itens do veículo registrada na saída.')
                    ->schema(static::getItemStatusFields('saida', $numeroRodasVeiculo)) // Reutiliza a função de campos
                    ->collapsible()
                    ->collapsed(fn(string $operation) => $operation === 'edit')
                    ->disabled(fn(string $operation) => $operation === 'edit'), // Desabilita para edição, apenas leitura

                // Seção para o Registro de Chegada (visível apenas na edição)
                Section::make('Informações de Chegada do Turno')
                    ->description('Preencha os detalhes quando o condutor retornar.')
                    ->visible(fn(string $operation) => $operation === 'edit')
                    ->schema([
                        Placeholder::make('status_chegada')
                            ->content(fn($record) => $record->momento_chegada ? 'Turno Finalizado' : 'Aguardando Chegada'),
                        Grid::make(3)
                            ->schema([
                                Forms\Components\DateTimePicker::make('momento_chegada')
                                    ->label('Data e Hora da Chegada')
                                    ->required()
                                    ->displayFormat('d/m/Y H:i')
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Quando a data/hora da chegada é preenchida, remove a obrigatoriedade
                                        // da quilometragem de chegada. (Ou pode fazer outras validações)
                                        //TODO: Avaliar...
                                    })
                                    ->columnSpan(1),
                                TextInput::make('km_chegada')
                                    ->label('Quilometragem na Chegada')
                                    ->numeric()
                                    ->step(10.00)
                                    ->suffix(' Km')
                                    ->required(fn(Forms\Get $get) => $get('momento_chegada') !== null) // Se tem hora de chegada, KM é obrigatória
                                    ->placeholder('Ex: 12450.99')
                                    ->columnSpan(1),
                                Select::make('nivel_combustivel_chegada')
                                    ->label('Nível de Combustível na Chegada')
                                    ->options(NivelCombustivelEnum::class)
                                    ->required(fn(Forms\Get $get) => $get('momento_chegada') !== null)
                                    ->native(false)
                                    ->columnSpan(1),
                                Select::make('id_encarregado_chegada')
                                    ->label('Encarregado na Chegada')
                                    ->relationship('encarregadoChegada', 'nome', fn(Builder $query) => $query->where('ativo', true)) //TODO: pode ajustar a condição
                                    ->getOptionLabelFromRecordUsing(fn(Pessoa $record) => "{$record->nome}") //TODO: pode retirar
                                    ->required(fn(Forms\Get $get) => $get('momento_chegada') !== null)
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
                    ->collapsible(),

                // Seção de Itens de Verificação na Chegada (visível apenas na edição)
                Section::make('Verificação de Itens do Veículo (Chegada)')
                    ->description('Marque a condição de cada item do veículo na chegada.')
                    ->visible(fn(string $operation) => $operation === 'edit')
                    ->schema(static::getItemStatusFields('chegada', $numeroRodasVeiculo)) // Reutiliza a função de campos
                    ->collapsible(),
            ]);

    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['itemStatus']))
            ->recordTitleAttribute('id_condutor')
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('momento_chegada')
                    ->label('Chegada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Aguardando...'),
                Tables\Columns\TextColumn::make('km_saida')
                    ->label('KM Saída')
                    ->numeric(0)
                    ->suffix(' Km'),
                Tables\Columns\TextColumn::make('km_chegada')
                    ->label('KM Chegada')
                    ->numeric(0)
                    ->suffix(' Km')
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('status_turno') // Coluna virtual para status do turno
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
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['id_bdv'] = $this->ownerRecord->id_bdv; // Atribui o ID do BDV pai TODO: interessante, me deu uma idéia para o modal de visualização
                        $data['cadastrado_por'] = Auth::id();
                        $data['atualizado_por'] = Auth::id();

                        $itemStatusSaidaData = $data['saida_status']; // Dados dos toggles de saída

                        // Inicia uma transação para garantir atomicidade
                        DB::beginTransaction();
//                        try {
//                            // Cria o registro de motorista
//                            $registroMotorista = $this->getRelationship()->create($data);
//
//                            // Cria o BdvItemStatus para Saída
//                            $itemStatusSaidaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
//                            $itemStatusSaidaData['tipo_registro'] = TipoRegistroStatusEnum::SAIDA;
//                            $itemStatusSaidaData['cadastrado_por'] = Auth::id();
//                            $itemStatusSaidaData['atualizado_por'] = Auth::id();
//                            BdvItemStatus::create($itemStatusSaidaData);
//
//                            DB::commit();
//                            return $registroMotorista->toArray();
//                        } catch (\Exception $e) {
//                            DB::rollBack();
//                            throw $e;
//                        }
                        try {
                            // Cria o registro de motorista (BdvRegistroMotorista)
                            // Passe apenas os campos que pertencem a BdvRegistroMotorista para create().
                            // Os campos 'saida_status' não fazem parte do fillable de BdvRegistroMotorista.
                            // Podemos criar um array filtrado ou usar o array $data diretamente e o fillable do model cuida do resto.
                            $registroMotorista = $this->getRelationship()->create($data); // O fillable do model BdvRegistroMotorista deve filtrar os campos.

                            // Cria o BdvItemStatus para Saída
                            $itemStatusSaidaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
                            $itemStatusSaidaData['tipo_registro'] = TipoRegistroStatusEnum::SAIDA;
                            $itemStatusSaidaData['cadastrado_por'] = Auth::id();
                            $itemStatusSaidaData['atualizado_por'] = Auth::id();
                            BdvItemStatus::create($itemStatusSaidaData);

                            DB::commit();
                            return $registroMotorista->toArray(); // Retorna os dados atualizados para o Filament
                        } catch (\Exception $e) {
                            DB::rollBack();
                            throw $e;
                        }
                    })
                    ->successNotificationTitle('Turno registrado com sucesso!'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth('7xl'),
                Tables\Actions\EditAction::make()
                    ->label('Finalizar Turno / Editar') // Renomeia a ação para indicar a finalização
                    ->icon('heroicon-o-clipboard-document-check')
                    ->modalWidth('7xl')
                    ->slideOver()
//                    ->mutateFormDataUsing(function (array $data): array {
//                        $data['atualizado_por'] = Auth::id();
//
//                        // Verifica se os campos de chegada foram preenchidos (indicando finalização)
//                        if (isset($data['momento_chegada']) && $data['momento_chegada'] !== null) {
//                            $itemStatusChegadaData = $data['chegada_status']; // Dados dos toggles de chegada
//                            unset($data['chegada_status']); // Remove do array principal
//
//                            // Obtém o registro de motorista atual (que está sendo editado)
//                            $registroMotorista = $this->getMountedTableActionRecord();
//
//                            // Inicia uma transação para garantir atomicidade
//                            DB::beginTransaction();
//                            try {
//                                // 1. Atualiza o registro de motorista com os dados de chegada
//                                $registroMotorista->update($data);
//
//                                // 2. Verifica se já existe um registro de chegada para este turno/motorista
//                                // Caso exista, atualiza. Caso contrário, cria.
//                                $existingChegadaStatus = BdvItemStatus::where('id_registro_motorista', $registroMotorista->id_registro_motorista)
//                                    ->where('tipo_registro', TipoRegistroStatusEnum::CHEGADA)
//                                    ->first();
//
//                                $itemStatusChegadaData['id_registro_motorista'] = $registroMotorista->id_registro_motorista;
//                                $itemStatusChegadaData['tipo_registro'] = TipoRegistroStatusEnum::CHEGADA;
//                                $itemStatusChegadaData['cadastrado_por'] = $existingChegadaStatus ? $existingChegadaStatus->cadastrado_por : Auth::id();
//                                $itemStatusChegadaData['atualizado_por'] = Auth::id();
//
//                                if ($existingChegadaStatus) {
//                                    $existingChegadaStatus->update($itemStatusChegadaData);
//                                } else {
//                                    BdvItemStatus::create($itemStatusChegadaData);
//                                }
//
//                                DB::commit();
//                                return $registroMotorista->toArray(); // Retorna os dados atualizados para o Filament
//                            } catch (\Exception $e) {
//                                DB::rollBack();
//                                throw $e;
//                            }
//                        }
//                        // Se não tem momento_chegada, apenas atualiza o registro de motorista (outros campos)
//                        return $data;
//                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['atualizado_por'] = Auth::id();

                        // Verifica se os campos de chegada foram preenchidos (indicando finalização)
                        if (isset($data['momento_chegada']) && $data['momento_chegada'] !== null) {
                            $itemStatusChegadaData = $data['chegada_status'] ?? []; // Dados dos toggles de chegada
                            // unset($data['chegada_status']); // <<< REMOVA ESTA LINHA!

                            // Obtém o registro de motorista atual (que está sendo editado)
                            $registroMotorista = $this->getMountedTableActionRecord();

                            // Inicia uma transação para garantir atomicidade
                            DB::beginTransaction();
                            try {
                                // 1. Atualiza o registro de motorista (BdvRegistroMotorista)
                                // Passe apenas os campos que pertencem a BdvRegistroMotorista para update().
                                // Os campos 'chegada_status' não fazem parte do fillable de BdvRegistroMotorista.
                                $registroMotorista->update($data); // O fillable do model BdvRegistroMotorista deve filtrar os campos.

                                // 2. Verifica se já existe um registro de chegada para este turno/motorista
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
                                return $registroMotorista->toArray(); // Retorna os dados atualizados para o Filament
                            } catch (\Exception $e) {
                                DB::rollBack();
                                throw $e;
                            }
                        }
                        // Se não tem momento_chegada, apenas atualiza o registro de motorista (outros campos)
                        // (os campos de BdvRegistroMotorista são atualizados pelo registroMotorista->update($data) acima)
                        return $data; // Retorna os dados originais para o Filament
                    })
                    ->fillForm(function ($record, Forms\Form $form) {
                        // Preenche o formulário com os atributos do registro.
                        // O afterStateHydrated nos Toggles cuidará de carregar os dados aninhados.
                        return $form->fill($record->toArray());
                    })
//                    ->fillForm(function ($record, Forms\Form $form) {
//                        // Preenche os campos do formulário para edição
//                        $formData = $record->toArray();
//
//                        // Preenche os campos de BdvItemStatus (saída e chegada)
//                        $saidaStatus = $record->itemStatuses->firstWhere('tipo_registro', TipoRegistroStatusEnum::SAIDA);
//                        if ($saidaStatus) {
//                            foreach ($saidaStatus->getAttributes() as $key => $value) {
//                                if (in_array($key, BdvItemStatus::BOOLEAN_FIELDS)) { // Supondo que você tenha uma constante para os campos booleanos no Model
//                                    $formData["saida_status.{$key}"] = $value;
//                                }
//                            }
//                        }
//
//                        $chegadaStatus = $record->itemStatuses->firstWhere('tipo_registro', TipoRegistroStatusEnum::CHEGADA);
//                        if ($chegadaStatus) {
//                            foreach ($chegadaStatus->getAttributes() as $key => $value) {
//                                if (in_array($key, BdvItemStatus::BOOLEAN_FIELDS)) {
//                                    $formData["chegada_status.{$key}"] = $value;
//                                }
//                            }
//                        }
//                        return $form->fill($formData);
//                    })
                    ->successNotificationTitle(fn ($record) => $record->momento_chegada ? 'Turno finalizado com sucesso!' : 'Registro atualizado com sucesso!'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nenhum registro de condutor para este BDV')
            ->emptyStateDescription('Adicione o primeiro turno para este Boletim Diário de Veículo.');
    }

    // Altere a função getItemStatusFields
    protected static function getItemStatusFields(string $prefix, int $numeroRodasVeiculo): array
    {
        // Esta função agora precisa de um $record, mas como é um método estático,
        // não temos acesso direto ao $record do formulário aqui.
        // A melhor forma é passar o $record para esta função, ou
        // usar um Fieldset que encapsule a lógica de "where" para os campos.

        // Uma forma de fazer é passar o $record para essa função,
        // mas aí precisaria de um contexto de Livewire (Forms\Get)
        // que não está disponível diretamente em métodos estáticos.

        // A alternativa é deixar os toggles com os nomes completos
        // (saida_status.campo, chegada_status.campo) e o Filament
        // fará o preenchimento se você tiver as relações no modelo BdvRegistroMotorista.

        // Vamos revisar a estrutura. Os campos já têm o prefixo correto,
        // então o Filament tentará preenchê-los automaticamente se
        // a relação existir no modelo e os dados estiverem no formato array.
        // O problema é que BdvItemStatus é um modelo separado.

        // Solução: Adicionar métodos `afterStateHydrated` e `beforeSave`
        // aos toggles para mapear explicitamente os valores.

        // No entanto, para a view, o jeito mais simples é usar `afterStateHydrated`
        // em cada toggle para buscar o valor correto do modelo BdvItemStatus.

        $getBooleanToggle = function (string $name, string $label, string $prefix) {
            return Toggle::make("{$prefix}_status.{$name}")
                ->label($label)
                ->default(true) // Valor padrão
                ->live() // Para reatividade
                ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set, ?BdvRegistroMotorista $record) use ($name, $prefix) {
                    // Este código será executado quando o formulário for preenchido (view ou edit)
                    if ($record) {
                        $tipoRegistro = ($prefix === 'saida') ? TipoRegistroStatusEnum::SAIDA : TipoRegistroStatusEnum::CHEGADA;
                        $itemStatus = $record->itemStatus->firstWhere('tipo_registro', $tipoRegistro->value);
                        if ($itemStatus && isset($itemStatus->{$name})) {
                            $set("{$prefix}_status.{$name}", (bool) $itemStatus->{$name});
                        } else {
                            // Se não encontrou o item ou o valor, use o default
                            $set("{$prefix}_status.{$name}", true);
                        }
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    // Este código será executado quando o formulário for salvo (edit ou create)
                    return (bool) $state;
                })
                ->disabled(fn (string $operation) => $operation === 'edit' && $prefix === 'saida' || $operation === 'view'); // Desabilitar na edição de saída e na visualização
        };

        $commonFields = [
            $getBooleanToggle('crlv', 'CRLV', $prefix),
            $getBooleanToggle('lacre_placa', 'Lacre Placa', $prefix),
            $getBooleanToggle('oleo_freio', 'Óleo Freio', $prefix),
            $getBooleanToggle('oleo_motor', 'Óleo Motor', $prefix),
            $getBooleanToggle('pneus_estado', 'Pneus Estado', $prefix),
            $getBooleanToggle('retrovisor_direito_esquerdo', 'Retrovisores D/E', $prefix),
            $getBooleanToggle('buzina', 'Buzina', $prefix),
            $getBooleanToggle('luzes_farol_alto_baixo_estacionamento', 'Luzes Farol (A/B/Estac.)', $prefix),
            $getBooleanToggle('luzes_pisca_re_freios', 'Luzes Pisca/Freios(Ré se 4 rodas)', $prefix),
            $getBooleanToggle('chaparia_pintura', 'Chaparia/Pintura', $prefix),
            $getBooleanToggle('giroflex', 'Giroflex', $prefix),
            $getBooleanToggle('sirene', 'Sirene', $prefix),
        ];

        // Repita a lógica para $twoWheelFields e $fourWheelFields
        $twoWheelFields = [
            $getBooleanToggle('velocimetro', 'Velocímetro', $prefix),
            $getBooleanToggle('bancos_estado', 'Bancos Estado', $prefix),
            $getBooleanToggle('bateria_agua', 'Bateria/Água', $prefix),
            $getBooleanToggle('paralamas_dianteiro_traseiro', 'Paralamas D/T', $prefix),
            $getBooleanToggle('descarga_completa', 'Descarga Completa', $prefix),
            $getBooleanToggle('etiqueta_revisao', 'Etiqueta Revisão', $prefix),
            $getBooleanToggle('tampas_laterais', 'Tampas Laterais', $prefix),
            $getBooleanToggle('protetor_perna', 'Protetor Perna', $prefix),
            $getBooleanToggle('fechadura_chave', 'Fechadura/Chave', $prefix),
            $getBooleanToggle('carenagem_tanque', 'Carenagem Tanque', $prefix),
            $getBooleanToggle('carenagem_farol', 'Carenagem Farol', $prefix),
            $getBooleanToggle('tanque_estrutura', 'Tanque Estrutura', $prefix),
            $getBooleanToggle('caixa_lado_esq_lado_dir', 'Caixa Lado E/D', $prefix),
            $getBooleanToggle('punhos_manete', 'Punhos/Manete', $prefix),
        ];

        $fourWheelFields = [
            $getBooleanToggle('macaco', 'Macaco', $prefix),
            $getBooleanToggle('chave_roda', 'Chave de Roda', $prefix),
            $getBooleanToggle('triangulo', 'Triângulo', $prefix),
            $getBooleanToggle('estepe', 'Estepe', $prefix),
            $getBooleanToggle('extintor', 'Extintor', $prefix),
            $getBooleanToggle('agua_radiador', 'Água Radiador', $prefix),
            $getBooleanToggle('calotas', 'Calotas', $prefix),
            $getBooleanToggle('retrovisor_interno', 'Retrovisor Interno', $prefix),
            $getBooleanToggle('macanetas_fechaduras', 'Maçanetas/Fechaduras', $prefix),
            $getBooleanToggle('limpadores', 'Limpadores', $prefix),
            $getBooleanToggle('luzes_internas', 'Luzes Internas', $prefix),
            $getBooleanToggle('cinto_seguranca', 'Cinto de Segurança', $prefix),
            $getBooleanToggle('radio_am_fm', 'Rádio AM/FM', $prefix),
            $getBooleanToggle('estofamento', 'Estofamento', $prefix),
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

    // ... (restante da classe, incluindo mutateFormDataUsing e fillForm nas actions) ...
    // É importante que o record que você está passando para fillForm() tenha a relação itemStatuses já carregada.
    // Certifique-se de que seu modelo BdvRegistroMotorista tenha a relação itemStatuses:
    // public function itemStatuses(): HasMany { return $this->hasMany(BdvItemStatus::class, 'id_registro_motorista'); }

}
