<?php

namespace App\Filament\Sevop\Resources;

use App\Enums\NivelCombustivelEnum;
use App\Enums\StatusVeiculo;
use App\Enums\TipoRegistroStatusEnum;
use App\Enums\TipoTurnoEnum;
use App\Filament\Sevop\Resources\BdvMainResource\Pages;
use App\Filament\Sevop\Resources\BdvMainResource\RelationManagers;
use App\Models\BdvMain;
use App\Models\Modelo; // Importar para a lógica de número de rodas
use App\Models\Pessoa; // Importar para o condutor e encarregados
use App\Models\Veiculo; // Importar para o veiculo
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BdvMainResource extends Resource
{
    protected static ?string $model = BdvMain::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Controle de Frota';
    protected static ?string $label = 'BDV';
    protected static ?string $pluralLabel = 'BDV´s';
    protected static ?int $navigationSort = 35; // Depois dos veículos

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Seção 1: Informações do BDV Principal
                Section::make('Informações do Boletim Diário de Veículo (BDV)')
                    ->description('Dados fundamentais para o registro do BDV.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('id_bdv')
                                    ->label('ID BDV')
                                    ->content(function (?BdvMain $record): \Illuminate\Contracts\Support\Htmlable {
                                        if ($record && $record->exists && $record->id_bdv) {
                                            $idFormatado = 'BDV-' . str_pad($record->id_bdv, 5, '0', STR_PAD_LEFT);

                                            return new \Illuminate\Support\HtmlString(
                                                '<div class="flex items-center">
                                                            <span class="text-primary-600 dark:text-primary-400 text-xl font-bold font-mono tracking-wider">
                                                                ' . htmlspecialchars($idFormatado) . '
                                                            </span>
                                                        </div>'
                                                                                    );
                                                                                }

                                                                                return new \Illuminate\Support\HtmlString(
                                                                                    '<div class="flex items-center">
                                                        <span class="text-gray-500 dark:text-gray-400 text-base italic">
                                                            Será gerado após salvar
                                                        </span>
                                                    </div>'
                                        );
                                    })
                                    ->columnSpan(1),
                                // Placeholder para exibir o número de rodas do veículo selecionado
                                Placeholder::make('numero_rodas_display')
                                    ->label('Tipo de Veículo (Rodas)')
                                    ->content(function (Forms\Get $get) {
                                        $numeroRodas = $get('numero_rodas_veiculo');
                                        if ($numeroRodas === 2) {
                                            return '2 Rodas (Moto)';
                                        } elseif ($numeroRodas === 4) {
                                            return '4 Rodas (SUV, Sedan, Van, etc.)';
                                        }
                                        return 'Selecione um veículo';
                                    })
                                    ->columnSpan(1),
                                Select::make('id_veiculo')
                                    ->label('Veículo')
                                    ->relationship(
                                        name: 'veiculo', // Nome do método de relacionamento no modelo BdvMain (public function veiculo(): BelongsTo)
                                        titleAttribute: 'placa', // Atributo principal para busca, mas a label será customizada abaixo
                                        // Adicionamos '?' antes de 'string' para indicar que é anulável
                                        // E adicionamos um 'when' para aplicar a busca apenas se $search existir
                                        modifyQueryUsing: fn (Builder $query, ?string $search) => $query
                                            ->where('status', StatusVeiculo::ATIVO->value)
                                            ->with('modelo')
                                            ->when($search, fn (Builder $q) => $q->where('placa', 'like', "%{$search}%")
                                                ->orWhere('prefixo_veiculo', 'like', "%{$search}%")),
                                    )
                                    // Define a label customizada para a opção selecionada e nos resultados da busca
                                    ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => "{$record->modelo->nome_modelo} - {$record->placa} - {$record->prefixo_veiculo}")
                                    ->required()
                                    ->searchable()
                                    ->preload() // Carrega as primeiras opções para melhorar a experiência
                                    ->live() // Essencial para o carregamento dinâmico do 'numero_rodas_veiculo'
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Quando o veículo muda, atualiza o campo oculto 'numero_rodas_veiculo'
                                        if ($state) {
                                            // Carrega o modelo do veículo com o relacionamento 'modelo'
                                            $veiculo = Veiculo::with('modelo')->find($state);
                                            $numeroRodas = $veiculo?->modelo?->numero_rodas ?? 0;
                                            $set('numero_rodas_veiculo', $numeroRodas);
                                        } else {
                                            $set('numero_rodas_veiculo', 0);
                                        }
                                    })
                                    ->columnSpan(1),
                                DatePicker::make('data_referencia')
                                    ->label('Data do BDV')
                                    ->required()
                                    ->live()
                                    ->default(now())
                                    ->displayFormat('d/m/Y')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            // Converte para Carbon e formata corretamente
                                            $novaDataSaida = \Carbon\Carbon::parse($state)->startOfDay();

                                            // Usa o formato que o DateTimePicker espera
                                            $set('bdv_registro_motorista.momento_saida', $novaDataSaida->format('Y-m-d\TH:i:s'));
                                        }
                                    })
                                    ->columnSpan(1),
                                Textarea::make('observacoes_gerais') //TODO: Pode ser retirado do formulário e da tabela
                                    ->label('Observações Gerais do BDV')
                                    ->maxLength(65535)
                                    ->rows(1)
                                    ->placeholder('Informações relevantes sobre o BDV, como anotações sobre o dia, etc.')
                                    ->columnSpan(2),
        //                            ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                // Campo oculto para armazenar o número de rodas para lógica de visibilidade
                Forms\Components\Hidden::make('numero_rodas_veiculo')
                    ->default(0),

                // Seção 2: Registro do Primeiro Motorista (Saída)
                Section::make('Primeiro Registro de Condutor (Saída)')
                    ->description('Detalhes do primeiro condutor e do estado do veículo na saída.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('bdv_registro_motorista.id_condutor')
                                    ->label('Condutor')
                                    ->searchable()
                                    ->preload() // Carrega algumas opções inicialmente para datasets pequenos
                                    ->getSearchResultsUsing(fn (string $search) => Pessoa::where('ativo', true)
                                        ->where(fn (Builder $query) => $query->where('nome', 'like', "%{$search}%")
                                            ->orWhere('cpf', 'like', "%{$search}%")->orWhere('matricula', 'like', "%{$search}%"))
                                        ->limit(50) // Limita os resultados para performance
                                        ->pluck('nome', 'rus_id')) // 'rus_id' é a chave primária da Pessoa
                                    ->getOptionLabelUsing(fn ($value): ?string => ($pessoa = Pessoa::find($value)) ? "{$pessoa->nome}": null)
//                                    ->getOptionLabelUsing(fn ($value): ?string => ($pessoa = Pessoa::find($value)) ? "{$pessoa->nome} (CPF: {$pessoa->cpf})" : null)
                                    ->required()
                                    ->live()
                                    ->columnSpan(1),
                                Select::make('bdv_registro_motorista.tipo_turno') // DEFINE O TURNO DA SAÍDA DO BDV: Matutino, Vespertino, Diurno, Noturno
                                    ->label('Turno de Ativação do Veículo')
                                    ->options(TipoTurnoEnum::class)
                                    ->required()
                                    ->live()
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan(1),
                                Forms\Components\DateTimePicker::make('bdv_registro_motorista.momento_saida')
                                    ->label('Data e Hora da Saída')
                                    ->required()
                                    ->live()
                                    ->default(now())
                                    ->displayFormat('d/m/Y H:i:s')
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            // Converte para Carbon e extrai apenas a data
                                            $novaDataReferencia = \Carbon\Carbon::parse($state)->toDateString();
                                            $set('data_referencia', $novaDataReferencia);
                                        }
                                    })
                                    ->columnSpan(1),
                                Select::make('bdv_registro_motorista.nivel_combustivel_saida')
                                    ->label('Nível de Combustível na Saída')
                                    ->options(NivelCombustivelEnum::class)
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->columnSpan(1),
                                TextInput::make('bdv_registro_motorista.km_saida')
                                    ->label('Quilometragem na Saída')
                                    ->numeric()
                                    ->step(1.00)
                                    ->suffix(' Km')
                                    ->required()
                                    ->live()
                                    ->placeholder('Ex: 12345.67')
                                    ->columnSpan(1),
                                Select::make('bdv_registro_motorista.id_encarregado_saida')
                                    ->label('Encarregado na Saída')
                                    ->searchable()
                                    ->preload()
                                    ->getSearchResultsUsing(fn (string $search) => Pessoa::where('ativo', true)
                                        ->where(fn (Builder $query) => $query->where('nome', 'like', "%{$search}%")
                                            ->orWhere('cpf', 'like', "%{$search}%")->orWhere('matricula', 'like', "%{$search}%"))
                                        ->limit(50)
                                        ->pluck('nome', 'rus_id'))
                                    ->getOptionLabelUsing(fn ($value): ?string => ($pessoa = Pessoa::find($value)) ? "{$pessoa->nome}" : null)
//                                    ->getOptionLabelUsing(fn ($value): ?string => ($pessoa = Pessoa::find($value)) ? "{$pessoa->nome} (CPF: {$pessoa->cpf})" : null)
//                                    ->required()
                                    ->columnSpan(2),
                            ]),
                        Textarea::make('bdv_registro_motorista.observacoes_saida')
                            ->label('Observações do Condutor na Saída')
                            ->maxLength(65535)
                            ->rows(2)
                            ->placeholder('Condições do veículo, observações do condutor ou encarregado, etc.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Seção 3: Itens de Verificação na Saída (Dinâmicos)
                Section::make('Verificação de Itens do Veículo (Saída)')
                    ->description('Marque a condição de cada item do veículo na saída.')
                    ->schema([
                        // Itens Comuns (sempre visíveis)
                        Forms\Components\Fieldset::make('Itens Comuns (2 e 4 Rodas)')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Toggle::make('bdv_item_status_saida.crlv')->default(true)->label('CRLV'),
                                        Toggle::make('bdv_item_status_saida.lacre_placa')->default(true)->label('Lacre Placa'),
                                        Toggle::make('bdv_item_status_saida.oleo_freio')->default(true)->label('Óleo Freio'),
                                        Toggle::make('bdv_item_status_saida.oleo_motor')->default(true)->label('Óleo Motor'),
                                        Toggle::make('bdv_item_status_saida.pneus_estado')->default(true)->label('Pneus Estado'),
                                        Toggle::make('bdv_item_status_saida.retrovisor_direito_esquerdo')->default(true)->label('Retrovisores D/E'),
                                        Toggle::make('bdv_item_status_saida.buzina')->default(true)->label('Buzina'),
                                        Toggle::make('bdv_item_status_saida.luzes_farol_alto_baixo_estacionamento')->default(true)->label('Luzes Farol (A/B/Estac.)'),
                                        Toggle::make('bdv_item_status_saida.luzes_pisca_re_freios')->default(true)->label('Luzes Pisca/Freios/(Ré se carro)'),
                                        Toggle::make('bdv_item_status_saida.chaparia_pintura')->default(true)->label('Chaparia/Pintura'),
                                        Toggle::make('bdv_item_status_saida.giroflex')->default(true)->label('Giroflex'),
                                        Toggle::make('bdv_item_status_saida.sirene')->default(true)->label('Sirene'),
                                    ])
                            ]),

                        // Itens Específicos para 2 Rodas (visibilidade condicional)
                        Forms\Components\Fieldset::make('Itens Específicos para 2 Rodas')
                            ->visible(fn (Forms\Get $get) => $get('numero_rodas_veiculo') == 2)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Toggle::make('bdv_item_status_saida.velocimetro')->default(true)->label('Velocímetro'),
                                        Toggle::make('bdv_item_status_saida.bancos_estado')->default(true)->label('Bancos Estado'),
                                        Toggle::make('bdv_item_status_saida.bateria_agua')->default(true)->label('Bateria/Água'),
                                        Toggle::make('bdv_item_status_saida.paralamas_dianteiro_traseiro')->default(true)->label('Paralamas D/T'),
                                        Toggle::make('bdv_item_status_saida.descarga_completa')->default(true)->label('Descarga Completa'),
                                        Toggle::make('bdv_item_status_saida.etiqueta_revisao')->default(true)->label('Etiqueta Revisão'),
                                        Toggle::make('bdv_item_status_saida.tampas_laterais')->default(true)->label('Tampas Laterais'),
                                        Toggle::make('bdv_item_status_saida.protetor_perna')->default(true)->label('Protetor Perna'),
                                        Toggle::make('bdv_item_status_saida.fechadura_chave')->default(true)->label('Fechadura/Chave'),
                                        Toggle::make('bdv_item_status_saida.carenagem_tanque')->default(true)->label('Carenagem Tanque'),
                                        Toggle::make('bdv_item_status_saida.carenagem_farol')->default(true)->label('Carenagem Farol'),
                                        Toggle::make('bdv_item_status_saida.tanque_estrutura')->default(true)->label('Tanque Estrutura'),
                                        Toggle::make('bdv_item_status_saida.caixa_lado_esq_lado_dir')->default(true)->label('Caixa Lado E/D'),
                                        Toggle::make('bdv_item_status_saida.punhos_manete')->default(true)->label('Punhos/Manete'),
                                    ])
                            ]),

                        // Itens Específicos para 4 Rodas (visibilidade condicional)
                        Forms\Components\Fieldset::make('Itens Específicos para 4 Rodas')
                            ->visible(fn (Forms\Get $get) => $get('numero_rodas_veiculo') == 4)
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Toggle::make('bdv_item_status_saida.macaco')->default(true)->label('Macaco'),
                                        Toggle::make('bdv_item_status_saida.chave_roda')->default(true)->label('Chave de Roda'),
                                        Toggle::make('bdv_item_status_saida.triangulo')->default(true)->label('Triângulo'),
                                        Toggle::make('bdv_item_status_saida.estepe')->default(true)->label('Estepe'),
                                        Toggle::make('bdv_item_status_saida.extintor')->default(true)->label('Extintor'),
                                        Toggle::make('bdv_item_status_saida.agua_radiador')->default(true)->label('Água Radiador'),
                                        Toggle::make('bdv_item_status_saida.calotas')->default(true)->label('Calotas'),
                                        Toggle::make('bdv_item_status_saida.retrovisor_interno')->default(true)->label('Retrovisor Interno'),
                                        Toggle::make('bdv_item_status_saida.macanetas_fechaduras')->default(true)->label('Maçanetas/Fechaduras'),
                                        Toggle::make('bdv_item_status_saida.limpadores')->default(true)->label('Limpadores'),
                                        Toggle::make('bdv_item_status_saida.luzes_internas')->default(true)->label('Luzes Internas'),
                                        Toggle::make('bdv_item_status_saida.cinto_seguranca')->default(true)->label('Cinto de Segurança'),
                                        Toggle::make('bdv_item_status_saida.radio_am_fm')->default(true)->label('Rádio AM/FM'),
                                        Toggle::make('bdv_item_status_saida.estofamento')->default(true)->label('Estofamento'),
                                    ])
                            ]),
                    ])
                    ->collapsible()
                    ->hidden(fn (Forms\Get $get) => !$get('id_veiculo')), // Esconde a seção até um veículo ser selecionado

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 10, 20, 50, 100, 'all'])
            ->defaultPaginationPageOption(5)
            ->defaultSort('data_referencia', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['veiculo.modelo'])) //atenção
            ->columns([
                Tables\Columns\TextColumn::make('id_bdv')
                    ->label('SEQ. BDV')
                    ->sortable()
                    ->numeric()
                    ->formatStateUsing(fn (string $state): string => 'BDV-' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->size('xl') // Define um tamanho de fonte extra grande
                    ->color('success') // Define a cor do texto para verde (cor de sucesso)
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('veiculo.prefixo_veiculo')
                    ->label('Prefixo Veículo')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_referencia')
                    ->label('Data do BDV')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('turnos_count')
                    ->label('Nº Turnos')
                    ->getStateUsing(function (BdvMain $record): int {
                        return $record->registrosMotorista()->count();
                    })
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'gray',
                        $state === 1 => 'success',
                        $state <= 3 => 'warning',
                        default => 'danger'
                    })
                    ->formatStateUsing(fn (int $state): string =>
                        $state . ' turno' . ($state !== 1 ? 's' : '')
                    ),
                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado Em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_veiculo')
                    ->label('Filtrar por Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('data_referencia')
                    ->form([
                        DatePicker::make('data_inicio')
                            ->label('Data Início')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('data_fim')
                            ->label('Data Fim')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_referencia', '>=', $date),
                            )
                            ->when(
                                $data['data_fim'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_referencia', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth('7xl'),
                Tables\Actions\EditAction::make()->modalWidth('7xl'),
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
            // Incluir o RelationManager para BdvRegistroMotorista aqui
            RelationManagers\BdvRegistroMotoristaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBdvMains::route('/'),
            'create' => Pages\CreateBdvMain::route('/create'),
            'view' => Pages\ViewBdvMain::route('/{record}'),
            'edit' => Pages\EditBdvMain::route('/{record}/edit'),
        ];
    }
}
