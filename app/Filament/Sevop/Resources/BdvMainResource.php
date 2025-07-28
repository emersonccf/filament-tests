<?php
//
//namespace App\Filament\Sevop\Resources;
//
//use App\Enums\NivelCombustivelEnum;
//use App\Enums\TipoRegistroStatusEnum;
//use App\Enums\TipoTurnoEnum;
//use App\Filament\Sevop\Resources\BdvMainResource\Pages;
//use App\Filament\Sevop\Resources\BdvMainResource\RelationManagers;
//use App\Models\BdvMain;
//use App\Models\Modelo; // Importar para a lógica de número de rodas
//use App\Models\Pessoa; // Importar para o condutor e encarregados
//use App\Models\Veiculo; // Importar para o veiculo
//use Filament\Forms;
//use Filament\Forms\Components\DatePicker;
//use Filament\Forms\Components\Grid;
//use Filament\Forms\Components\Placeholder;
//use Filament\Forms\Components\Section;
//use Filament\Forms\Components\Select;
//use Filament\Forms\Components\Textarea;
//use Filament\Forms\Components\TextInput;
//use Filament\Forms\Components\TimePicker;
//use Filament\Forms\Components\Toggle;
//use Filament\Forms\Form;
//use Filament\Resources\Resource;
//use Filament\Tables;
//use Filament\Tables\Table;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Support\Facades\Auth;
//
//class BdvMainResource extends Resource
//{
//    protected static ?string $model = BdvMain::class;
//    protected static ?string $navigationIcon = 'heroicon-o-document-check';
//    protected static ?string $navigationGroup = 'Controle de Frota';
//    protected static ?int $navigationSort = 35; // Depois dos veículos
//
//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                // Seção 1: Informações do BDV Principal
//                Section::make('Informações do Boletim Diário de Veículo (BDV)')
//                    ->description('Dados fundamentais para o registro do BDV.')
//                    ->schema([
//                        Grid::make(3)
//                            ->schema([
//                                Select::make('id_veiculo')
//                                    ->label('Veículo')
//                                    ->relationship('veiculo', 'placa') // Exibe a placa
//                                    ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => "{$record->modelo->nome_modelo} - {$record->placa} - {$record->prefixo_veiculo}")
//                                    ->required()
//                                    ->searchable()
//                                    ->preload()
//                                    ->live() // Essencial para o carregamento dinâmico
//                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
//                                        // Quando o veículo muda, podemos resetar os campos de status ou fazer algo mais complexo
//                                        // Para o propósito de exemplo, vamos apenas exibir o número de rodas.
//                                        if ($state) {
//                                            $veiculo = Veiculo::all()->find($state);
//                                            $numeroRodas = $veiculo?->modelo?->numero_rodas ?? 0;
//                                            $set('numero_rodas_veiculo', $numeroRodas); // Armazena em um campo oculto ou placeholder
//                                        } else {
//                                            $set('numero_rodas_veiculo', 0);
//                                        }
//                                    })
//                                    ->columnSpan(1),
//                                DatePicker::make('data_referencia')
//                                    ->label('Data do BDV')
//                                    ->required()
//                                    ->default(now())
//                                    ->displayFormat('d/m/Y')
//                                    ->columnSpan(1),
//                                // Placeholder para exibir o número de rodas do veículo selecionado
//                                Placeholder::make('numero_rodas_display')
//                                    ->label('Tipo de Veículo (Rodas)')
//                                    ->content(function (Forms\Get $get) {
//                                        $numeroRodas = $get('numero_rodas_veiculo');
//                                        if ($numeroRodas === 2) {
//                                            return '2 Rodas (Moto)';
//                                        } elseif ($numeroRodas === 4) {
//                                            return '4 Rodas (SUV, Sedan, Van, etc.)';
//                                        }
//                                        return 'Selecione um veículo';
//                                    })
//                                    ->columnSpan(1),
//                            ]),
//                        Textarea::make('observacoes_gerais')
//                            ->label('Observações Gerais do BDV')
//                            ->maxLength(65535)
//                            ->rows(3)
//                            ->placeholder('Informações relevantes sobre o BDV, como anotações sobre o dia, etc.')
//                            ->columnSpanFull(),
//                    ])
//                    ->collapsible(),
//
//                // Campo oculto para armazenar o número de rodas para lógica de visibilidade
//                Forms\Components\Hidden::make('numero_rodas_veiculo')
//                    ->default(0),
//
//                // Seção 2: Registro do Primeiro Motorista (Ex. Saída - Matutino)
//                Section::make('Primeiro Registro de Condutor (Saída)')
//                    ->description('Detalhes do primeiro condutor e do estado do veículo na saída.')
//                    ->schema([
//                        Grid::make(3)
//                            ->schema([
//                                Select::make('bdv_registro_motorista.id_condutor') // Nomeando com prefixo para fácil manipulação
//                                ->label('Condutor')
//                                    ->relationship('condutor', 'nome', fn (Builder $query) => $query->where('ativo', true)) // Apenas pessoas ativas
//                                    ->getOptionLabelFromRecordUsing(fn (Pessoa $record) => "{$record->nome} (CPF: {$record->cpf})")
//                                    ->required()
//                                    ->searchable()
//                                    ->preload()
//                                    ->columnSpan(1),
//                                Select::make('bdv_registro_motorista.tipo_turno')
//                                    ->label('Turno de Ativação do Veículo')
//                                    ->options(TipoTurnoEnum::class)
//                                    ->required()
//                                    ->searchable()
//                                    ->native(false)
//                                    ->columnSpan(1),
//                                Forms\Components\DateTimePicker::make('bdv_registro_motorista.momento_saida')
//                                    ->label('Data e Hora da Saída')
//                                    ->required()
//                                    ->default(now())
//                                    ->displayFormat('d/m/Y H:i:s')
//                                    ->columnSpan(1),
//                                Select::make('bdv_registro_motorista.nivel_combustivel_saida')
//                                    ->label('Nível de Combustível na Saída')
//                                    ->options(NivelCombustivelEnum::class)
//                                    ->required()
//                                    ->native(false)
//                                    ->columnSpan(1),
//                                TextInput::make('bdv_registro_motorista.km_saida')
//                                    ->label('Quilometragem na Saída')
//                                    ->numeric()
//                                    ->step(10.00)
//                                    ->suffix(' Km')
//                                    ->required()
//                                    ->placeholder('Ex: 12345.67')
//                                    ->columnSpan(1),
//                                Select::make('bdv_registro_motorista.id_encarregado_saida')
//                                    ->label('Encarregado na Saída')
//                                    ->relationship('encarregadoSaida', 'nome', fn (Builder $query) => $query->where('ativo', true)) // Apenas pessoas ativas
//                                    ->getOptionLabelFromRecordUsing(fn (Pessoa $record) => "{$record->nome} (CPF: {$record->cpf})")
////                                    ->required()
//                                    ->searchable()
//                                    ->preload()
//                                    ->columnSpan(2),
//                            ]),
//                        Textarea::make('bdv_registro_motorista.observacoes_saida')
//                            ->label('Observações do Condutor na Saída')
//                            ->maxLength(65535)
//                            ->rows(2)
//                            ->placeholder('Condições do veículo, observações do condutor ou encarregado, etc.')
//                            ->columnSpanFull(),
//                    ])
//                    ->collapsible(),
//
//                // Seção 3: Itens de Verificação na Saída (Dinâmicos)
//                Section::make('Verificação de Itens do Veículo (Saída)')
//                    ->description('Marque a condição de cada item do veículo na saída.')
//                    ->schema([
//                        // Itens Comuns (sempre visíveis)
//                        Forms\Components\Fieldset::make('Itens Comuns (2 e 4 Rodas)')
//                            ->schema([
//                                Grid::make(4)
//                                    ->schema([
//                                        Toggle::make('bdv_item_status_saida.crlv')->label('CRLV'),
//                                        Toggle::make('bdv_item_status_saida.lacre_placa')->label('Lacre Placa'),
//                                        Toggle::make('bdv_item_status_saida.oleo_freio')->label('Óleo Freio'),
//                                        Toggle::make('bdv_item_status_saida.oleo_motor')->label('Óleo Motor'),
//                                        Toggle::make('bdv_item_status_saida.pneus_estado')->label('Pneus Estado'),
//                                        Toggle::make('bdv_item_status_saida.retrovisor_direito_esquerdo')->label('Retrovisores D/E'),
//                                        Toggle::make('bdv_item_status_saida.buzina')->label('Buzina'),
//                                        Toggle::make('bdv_item_status_saida.luzes_farol_alto_baixo_estacionamento')->label('Luzes Farol (A/B/Estac.)'),
//                                        Toggle::make('bdv_item_status_saida.luzes_pisca_re_freios')->label('Luzes Pisca/Freios/(Ré se carro)'),
//                                        Toggle::make('bdv_item_status_saida.chaparia_pintura')->label('Chaparia/Pintura'),
//                                        Toggle::make('bdv_item_status_saida.giroflex')->label('Giroflex'),
//                                        Toggle::make('bdv_item_status_saida.sirene')->label('Sirene'),
//                                    ])
//                            ]),
//
//                        // Itens Específicos para 2 Rodas (visibilidade condicional)
//                        Forms\Components\Fieldset::make('Itens Específicos para 2 Rodas')
//                            ->visible(fn (Forms\Get $get) => $get('numero_rodas_veiculo') == 2)
//                            ->schema([
//                                Grid::make(4)
//                                    ->schema([
//                                        Toggle::make('bdv_item_status_saida.velocimetro')->label('Velocímetro'),
//                                        Toggle::make('bdv_item_status_saida.bancos_estado')->label('Bancos Estado'),
//                                        Toggle::make('bdv_item_status_saida.bateria_agua')->label('Bateria/Água'),
//                                        Toggle::make('bdv_item_status_saida.paralamas_dianteiro_traseiro')->label('Paralamas D/T'),
//                                        Toggle::make('bdv_item_status_saida.descarga_completa')->label('Descarga Completa'),
//                                        Toggle::make('bdv_item_status_saida.etiqueta_revisao')->label('Etiqueta Revisão'),
//                                        Toggle::make('bdv_item_status_saida.tampas_laterais')->label('Tampas Laterais'),
//                                        Toggle::make('bdv_item_status_saida.protetor_perna')->label('Protetor Perna'),
//                                        Toggle::make('bdv_item_status_saida.fechadura_chave')->label('Fechadura/Chave'),
//                                        Toggle::make('bdv_item_status_saida.carenagem_tanque')->label('Carenagem Tanque'),
//                                        Toggle::make('bdv_item_status_saida.carenagem_farol')->label('Carenagem Farol'),
//                                        Toggle::make('bdv_item_status_saida.tanque_estrutura')->label('Tanque Estrutura'),
//                                        Toggle::make('bdv_item_status_saida.caixa_lado_esq_lado_dir')->label('Caixa Lado E/D'),
//                                        Toggle::make('bdv_item_status_saida.punhos_manete')->label('Punhos/Manete'),
//                                    ])
//                            ]),
//
//                        // Itens Específicos para 4 Rodas (visibilidade condicional)
//                        Forms\Components\Fieldset::make('Itens Específicos para 4 Rodas')
//                            ->visible(fn (Forms\Get $get) => $get('numero_rodas_veiculo') == 4)
//                            ->schema([
//                                Grid::make(4)
//                                    ->schema([
//                                        Toggle::make('bdv_item_status_saida.macaco')->label('Macaco'),
//                                        Toggle::make('bdv_item_status_saida.chave_roda')->label('Chave de Roda'),
//                                        Toggle::make('bdv_item_status_saida.triangulo')->label('Triângulo'),
//                                        Toggle::make('bdv_item_status_saida.estepe')->label('Estepe'),
//                                        Toggle::make('bdv_item_status_saida.extintor')->label('Extintor'),
//                                        Toggle::make('bdv_item_status_saida.agua_radiador')->label('Água Radiador'),
//                                        Toggle::make('bdv_item_status_saida.calotas')->label('Calotas'),
//                                        Toggle::make('bdv_item_status_saida.retrovisor_interno')->label('Retrovisor Interno'),
//                                        Toggle::make('bdv_item_status_saida.macanetas_fechaduras')->label('Maçanetas/Fechaduras'),
//                                        Toggle::make('bdv_item_status_saida.limpadores')->label('Limpadores'),
//                                        Toggle::make('bdv_item_status_saida.luzes_internas')->label('Luzes Internas'),
//                                        Toggle::make('bdv_item_status_saida.cinto_seguranca')->label('Cinto de Segurança'),
//                                        Toggle::make('bdv_item_status_saida.radio_am_fm')->label('Rádio AM/FM'),
//                                        Toggle::make('bdv_item_status_saida.estofamento')->label('Estofamento'),
//                                    ])
//                            ]),
//                    ])
//                    ->collapsible()
//                    ->hidden(fn (Forms\Get $get) => !$get('id_veiculo')), // Esconde a seção até um veículo ser selecionado
//            ]);
//    }
//
//    public static function table(Table $table): Table
//    {
//        return $table
//            ->paginationPageOptions([5, 10, 20, 50, 100, 'all'])
//            ->defaultPaginationPageOption(5)
//            ->defaultSort('data_referencia', 'desc')
//            ->columns([
//                Tables\Columns\TextColumn::make('veiculo.prefixo_veiculo')
//                    ->label('Prefixo Veículo')
//                    ->sortable()
//                    ->searchable()
//                    ->weight('bold')
//                    ->badge()
//                    ->color('primary'),
//                Tables\Columns\TextColumn::make('veiculo.placa')
//                    ->label('Placa')
//                    ->sortable()
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('data_referencia')
//                    ->label('Data do BDV')
//                    ->date('d/m/Y')
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('registrosMotorista.count')
//                    ->label('Nº Turnos')
//                    ->counts('registrosMotorista')
//                    ->alignCenter(),
//                Tables\Columns\TextColumn::make('userCreatedBy.name')
//                    ->label('Cadastrado Por')
//                    ->sortable()
//                    ->searchable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->label('Criado Em')
//                    ->dateTime('d/m/Y H:i:s')
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//            ])
//            ->filters([
//                Tables\Filters\SelectFilter::make('id_veiculo')
//                    ->label('Filtrar por Veículo')
//                    ->relationship('veiculo', 'placa')
//                    ->searchable()
//                    ->preload(),
//                Tables\Filters\Filter::make('data_referencia')
//                    ->form([
//                        DatePicker::make('data_inicio')
//                            ->label('Data Início')
//                            ->displayFormat('d/m/Y'),
//                        DatePicker::make('data_fim')
//                            ->label('Data Fim')
//                            ->displayFormat('d/m/Y'),
//                    ])
//                    ->query(function (Builder $query, array $data): Builder {
//                        return $query
//                            ->when(
//                                $data['data_inicio'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('data_referencia', '>=', $date),
//                            )
//                            ->when(
//                                $data['data_fim'],
//                                fn (Builder $query, $date): Builder => $query->whereDate('data_referencia', '<=', $date),
//                            );
//                    }),
//            ])
//            ->actions([
//                Tables\Actions\ViewAction::make()->modalWidth('7xl'),
//                Tables\Actions\EditAction::make()->modalWidth('7xl'),
//                Tables\Actions\DeleteAction::make(),
//            ])
//            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
//            ]);
//    }
//
//    public static function getRelations(): array
//    {
//        return [
//            // Incluiremos o RelationManager para BdvRegistroMotorista aqui
//            RelationManagers\BdvRegistroMotoristaRelationManager::class,
//        ];
//    }
//
//    public static function getPages(): array
//    {
//        return [
//            'index' => Pages\ListBdvMains::route('/'),
//            'create' => Pages\CreateBdvMain::route('/create'),
//            'view' => Pages\ViewBdvMain::route('/{record}'),
//            'edit' => Pages\EditBdvMain::route('/{record}/edit'),
//        ];
//    }
//}
