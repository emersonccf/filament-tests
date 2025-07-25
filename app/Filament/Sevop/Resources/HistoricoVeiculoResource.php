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

// NOVOS IMPORTS NECESSÁRIOS:
use Filament\Forms\Components\TextInput; // Garanta que este import está presente
use App\Models\Veiculo;   // Para a relação com Veículo
use App\Models\User;     // Para as relações de usuário (cadastrado_por, atualizado_por)
use App\Enums\TipoEventoHistorico; // Imports dos Enums
use App\Enums\PrioridadeHistorico;
use App\Enums\StatusEventoHistorico;
use Filament\Forms\Components\Select;     // Para caixas de combinação
use Filament\Forms\Components\Placeholder; // Para exibir informações não editáveis
use Filament\Forms\Components\Hidden;     // Para ocultar campos preenchidos automaticamente
use Filament\Forms\Components\TimePicker; // Para o campo hora_evento
use Illuminate\Support\Facades\Auth;      // Para acessar o usuário logado
use Filament\Forms\Components\Section;    // Para organizar melhor o formulário

class HistoricoVeiculoResource extends Resource
{
    protected static ?string $model = HistoricoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 60; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalhes do Evento')
                    ->schema([
                        // Campo de Chave Estrangeira: id_veiculo
                        Select::make('id_veiculo')
                            ->label('Veículo (Placa)')
                            ->relationship(
                                name: 'veiculo',
                                titleAttribute: 'placa', // Voltar para uma coluna real do banco
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->with('modelo') // Eager load o relacionamento 'modelo'
                            )
                            ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => $record->placa_modelo_direcionamento)
//                            ->getSearchResultsUsing(function (string $search) {
//                                return Veiculo::where('direcionamento', DirecionamentoVeiculo::RESERVA)
//                                    ->with('modelo')
//                                    ->where(function ($query) use ($search) {
//                                        $query->where('placa', 'like', "%{$search}%")
//                                            ->orWhereHas('modelo', function ($q) use ($search) {
//                                                $q->where('nome_modelo', 'like', "%{$search}%");
//                                            });
//                                    })
//                                    ->limit(50)
//                                    ->get()
//                                    ->mapWithKeys(fn (Veiculo $veiculo) => [
//                                        $veiculo->id_veiculo => $veiculo->placa_modelo_direcionamento
//                                    ]);
//                            })
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        // Campo de Enum: tipo_evento
                        Select::make('tipo_evento')
                            ->label('Tipo de Evento')
                            ->options(TipoEventoHistorico::class)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('teve_vitima')
                            ->label('Teve vítima?')
                            ->inline(false) // Coloca o label acima do toggle
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_evento')
                            ->label('Data do Evento')
                            ->required()
                            ->columnSpan(1),

                        TimePicker::make('hora_evento')
                            ->label('Hora do Evento')
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('quilometragem')
                            ->numeric()
                            ->step('0.01') // Permite decimais
                            ->nullable()
                            ->columnSpan(1),

                        // Campo de Enum: prioridade
                        Select::make('prioridade')
                            ->label('Prioridade')
                            ->options(PrioridadeHistorico::class)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('afeta_disponibilidade')
                            ->label('Afeta Disponibilidade?')
                            ->inline(false) // Coloca o label acima do toggle
                            ->columnSpan(1),

                        Select::make('id_veiculo_substituto')
                            ->label('Veículo Substituto Reserva')
                            ->relationship(
                                name: 'veiculoSubstituto',
                                titleAttribute: 'placa',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('direcionamento', DirecionamentoVeiculo::RESERVA)
                                    ->with('modelo')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Veiculo $record) => $record->placa_modelo_direcionamento)
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        // Campo de Enum: status_evento
                        Select::make('status_evento')
                            ->label('Status do Evento')
                            ->options(StatusEventoHistorico::class)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('descricao')
                            ->label('Descrição')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('local_ocorrencia')
                            ->label('Local da Ocorrência')
                            ->maxLength(200)
                            ->default(null)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? mb_strtoupper($state) : null) // Para maiúsculas, permite nulo
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('prestador_servico')
                            ->label('Prestador de Serviço')
                            ->maxLength(100)
                            ->default(null)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? mb_strtoupper($state) : null) // Para maiúsculas, permite nulo
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_prevista_conclusao')
                            ->label('Data Prevista Conclusão')
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_conclusao')
                            ->label('Data de Conclusão')
                            ->nullable()
                            ->columnSpan(1),

                        TimePicker::make('hora_conclusao')
                            ->label('Hora retorno veículo')
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('observacoes')
                            ->label('Observações Adicionais')
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Informações de Auditoria')
                    ->schema([
                        Hidden::make('cadastrado_por'),
                        Hidden::make('atualizado_por'),
                        Placeholder::make('cadastrado_por_display')
                            ->label('Cadastrado Por')
                            ->content(function (string $operation, ?HistoricoVeiculo $record): string {
                                if ($operation === 'create') {
                                    return Auth::user()->name;
                                }
                                return $record?->userCreatedBy?->name ?? 'N/A';
                            })
                            ->columnSpan(1),
                        Placeholder::make('atualizado_por_display')
                            ->label('Atualizado Por')
                            ->content(function (string $operation, ?HistoricoVeiculo $record): string {
                                if ($operation === 'create') {
                                    return Auth::user()->name;
                                }
                                return $record?->userUpdatedBy?->name ?? 'N/A';
                            })
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 10]) // Limita para APENAS 5 a 10 registros por página
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa_modelo_direcionamento')
                    ->label('Veículo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_evento')
                    ->searchable()
                    ->badge(), // Exibe o Enum como um badge
                Tables\Columns\IconColumn::make('teve_vitima')
                    ->label('Teve Vitima?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('data_evento')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora_evento')
                    ->time('H:i') // Formato de hora
                    ->sortable(),
                Tables\Columns\TextColumn::make('quilometragem')
                    ->numeric(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('prioridade')
                    ->searchable()
                    ->badge(), // Exibe o Enum como um badge
                Tables\Columns\IconColumn::make('afeta_disponibilidade')
                    ->label('Afeta Dispon.')
                    ->boolean(),
                Tables\Columns\TextColumn::make('veiculoSubstituto.placa_modelo_direcionamento')
                    ->label('Veículo Substituto'),
//                    ->searchable(query: function (Builder $query, string $search): Builder {
//                        return $query->whereHas('veiculoSubstituto', function ($q) use ($search) {
//                            $q->where('placa', 'like', "%{$search}%")
//                                ->orWhereHas('modelo', function ($subQ) use ($search) {
//                                    $subQ->where('nome_modelo', 'like', "%{$search}%");
//                                });
//                        });
//                    })
//                    ->sortable(query: function (Builder $query, string $direction): Builder {
//                        return $query->join('veiculos as v_substituto', 'v_substituto.id_veiculo', '=', $query->getModel()->getTable() . '.id_veiculo_substituto')
//                            ->join('modelos as m_substituto', 'm_substituto.id_modelo', '=', 'v_substituto.id_modelo')
//                            ->orderBy('v_substituto.placa', $direction);
//                    }),
                Tables\Columns\TextColumn::make('status_evento')
                    ->searchable()
                    ->badge(), // Exibe o Enum como um badge
                Tables\Columns\TextColumn::make('descricao')
                    ->wrap()
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('local_ocorrencia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('prestador_servico')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('data_prevista_conclusao')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('data_conclusao')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hora_conclusao')
                    ->time('H:i') // Formato de hora
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userUpdatedBy.name')
                    ->label('Atualizado Por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-M-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_veiculo')
                    ->label('Filtrar por Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('tipo_evento')
                    ->label('Filtrar por Tipo de Evento')
                    ->options(TipoEventoHistorico::class),
                Tables\Filters\SelectFilter::make('prioridade')
                    ->label('Filtrar por Prioridade')
                    ->options(PrioridadeHistorico::class),
                Tables\Filters\SelectFilter::make('status_evento')
                    ->label('Filtrar por Status do Evento')
                    ->options(StatusEventoHistorico::class),
                Tables\Filters\TernaryFilter::make('afeta_disponibilidade')
                    ->label('Afeta Disponibilidade?')
                    ->placeholder('Ambos') // Método correto para o rótulo da opção nula/todas
                    ->trueLabel('Sim')
                    ->falseLabel('Não')
                    ->nullable(), // Adicione este método para lidar com valores nulos no banco de dados
                Tables\Filters\Filter::make('data_evento')
                    ->form([
                        Forms\Components\DatePicker::make('data_evento_from')
                            ->label('Data do Evento (De)'),
                        Forms\Components\DatePicker::make('data_evento_until')
                            ->label('Data do Evento (Até)'),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
