<?php

namespace App\Filament\Sevop\Resources\VeiculoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput; // Importar TextInput
use Filament\Forms\Components\Section;   // Importar Section
use Illuminate\Support\Facades\Auth;
use App\Enums\PrioridadeHistorico;
use App\Enums\StatusEventoHistorico;
use App\Enums\TipoEventoHistorico;

class HistoricoVeiculosRelationManager extends RelationManager
{
    // Define a relação que este RelationManager gerencia no Model pai (Veiculo)
    protected static string $relationship = 'historicos';

    // Título que aparecerá na aba/seção na página de edição do veículo
    protected static ?string $title = 'Histórico do Veículo';

    // Método para definir o formulário de criação/edição de um registro de histórico
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalhes do Evento')
                    ->schema([
                        // O campo id_veiculo NÃO é necessário aqui, pois a relação já o vincula automaticamente
                        // ao veículo pai que está sendo editado.

//                        Select::make('tipo_evento')
//                            ->label('Tipo de Evento')
//                            ->options(TipoEventoHistorico::class)
//                            ->required()
//                            ->searchable()
//                            ->native(false) // Renderiza um select personalizado do Filament
//                            ->columnSpan(1),
//
//                        Forms\Components\DatePicker::make('data_evento')
//                            ->label('Data do Evento')
//                            ->required()
//                            ->native(false) // Para usar o DatePicker do Filament
//                            ->default(now()) // Sugere a data atual
//                            ->columnSpan(1),
//
//                        TimePicker::make('hora_evento')
//                            ->label('Hora do Evento')
//                            ->nullable() // Permite nulo, conforme seu Model/Resource
//                            ->seconds(false) // Não mostra segundos para simplificar
//                            ->default(now()->startOfMinute()) // Sugere a hora atual sem segundos
//                            ->columnSpan(1),
//
//                        TextInput::make('quilometragem')
//                            ->numeric()
//                            ->step('0.01') // Permite decimais
//                            ->nullable()
//                            ->columnSpan(1),
//
//                        Select::make('prioridade')
//                            ->label('Prioridade')
//                            ->options(PrioridadeHistorico::class)
//                            ->required()
//                            ->searchable()
//                            ->native(false)
//                            ->columnSpan(1),
//
//                        Toggle::make('afeta_disponibilidade')
//                            ->label('Afeta Disponibilidade?')
//                            ->default(false)
//                            ->inline(false) // Alinha o label acima do toggle, conforme seu Resource
//                            ->columnSpan(1),

                        Select::make('status_evento')
                            ->label('Status do Evento')
                            ->options(StatusEventoHistorico::class)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->columnSpan(1),

                        Textarea::make('descricao')
                            ->label('Descrição')
                            ->required()
                            ->disabled()
                            ->maxLength(65535)
                            ->columnSpanFull(), // Ocupa a largura total da seção

                        TextInput::make('local_ocorrencia')
                            ->label('Local da Ocorrência')
                            ->maxLength(200)
                            ->nullable()
                            ->disabled() // desabilitado
                            ->columnSpan(1),

                        TextInput::make('prestador_servico')
                            ->label('Prestador de Serviço')
                            ->maxLength(100)
                            ->nullable()
                            ->disabled() // desabilitado
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_prevista_conclusao')
                            ->label('Data Prevista Conclusão')
                            ->nullable()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_conclusao')
                            ->label('Data de Conclusão')
                            ->nullable()
                            ->native(false)
                            ->columnSpan(1),

                        Textarea::make('observacoes')
                            ->label('Observações Adicionais')
                            ->maxLength(65535)
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2), // Organiza os campos em 2 colunas dentro desta seção

                Section::make('Informações de Auditoria')
                    ->schema([
                        // Campos ocultos que serão preenchidos automaticamente
                        Hidden::make('cadastrado_por'),
                        Hidden::make('atualizado_por'),

                        // Placeholders para exibir o nome dos usuários (não editáveis)
                        Placeholder::make('cadastrado_por_display')
                            ->label('Cadastrado Por')
                            ->content(function (string $operation, ?\App\Models\HistoricoVeiculo $record): string {
                                // Se for criação, exibe o nome do usuário logado
                                if ($operation === 'create') {
                                    return Auth::user()->name;
                                }
                                // Se for edição, busca o nome do usuário que criou o registro
                                return $record?->userCreatedBy?->name ?? 'N/A';
                            })
                            ->visibleOn('edit') // Visível apenas na edição de um registro existente
                            ->columnSpan(1),

                        Placeholder::make('atualizado_por_display')
                            ->label('Atualizado Por')
                            ->content(function (string $operation, ?\App\Models\HistoricoVeiculo $record): string {
                                // Se for criação, exibe o nome do usuário logado
                                if ($operation === 'create') {
                                    return Auth::user()->name;
                                }
                                // Se for edição, busca o nome do usuário que fez a última atualização
                                return $record?->userUpdatedBy?->name ?? 'N/A';
                            })
                            ->visibleOn('edit') // Visível apenas na edição de um registro existente
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    // Método para definir a tabela de listagem dos registros de histórico
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipo_evento') // Usado para o título em algumas ações (ex: "Excluir tipo_evento?")
            ->paginationPageOptions([5]) // Limita para APENAS 5 registros por página
            ->columns([
                // Coluna para a placa do veículo pai, conforme solicitado
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipo_evento')
                    ->label('Tipo de Evento')
                    ->badge() // Exibe o Enum como um badge (formato visual agradável)
                    ->searchable(),

                Tables\Columns\TextColumn::make('data_evento')
                    ->label('Data')
                    ->date('d/m/Y') // Formata a data para dd/mm/aaaa
                    ->sortable(),

                Tables\Columns\TextColumn::make('hora_evento')
                    ->label('Hora')
                    ->time('H:i') // Formata a hora para HH:mm
                    ->sortable(),

                Tables\Columns\TextColumn::make('prioridade')
                    ->label('Prioridade')
                    ->badge()
                    // Adiciona cores aos badges com base na prioridade
                    ->color(fn (PrioridadeHistorico $state): string => match ($state) { // CORRIGIDO: tipagem para o Enum
                        PrioridadeHistorico::BAIXA => 'gray',   // CORRIGIDO: match direto com o Enum case
                        PrioridadeHistorico::MEDIA => 'info',
                        PrioridadeHistorico::ALTA => 'warning',
                        PrioridadeHistorico::CRITICA => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\IconColumn::make('afeta_disponibilidade')
                    ->label('Afeta Disp.')
                    ->boolean(), // Exibe um ícone (check/x) para valores booleanos

                Tables\Columns\TextColumn::make('status_evento')
                    ->label('Status')
                    ->badge()
                    // Adiciona cores aos badges com base no status do evento
                    ->color(fn (StatusEventoHistorico $state): string => match ($state) { // CORRIGIDO: tipagem para o Enum
                        StatusEventoHistorico::PENDENTE => 'warning',   // CORRIGIDO: match direto com o Enum case
                        StatusEventoHistorico::EM_ANDAMENTO => 'info',
                        StatusEventoHistorico::CONCLUIDO => 'success',
                        StatusEventoHistorico::CANCELADO => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                // Campos adicionais do histórico, ocultos por padrão para manter a tabela limpa
                Tables\Columns\TextColumn::make('quilometragem')
                    ->numeric(0) // Sem casas decimais para exibição mais limpa se preferir
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por padrão

                Tables\Columns\TextColumn::make('descricao')
                    ->wrap() // Quebra o texto em múltiplas linhas
                    ->limit(50) // Limita a 50 caracteres antes de truncar
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('local_ocorrencia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('prestador_servico')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data_prevista_conclusao')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('data_conclusao')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('observacoes')
                    ->wrap()
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Campos de auditoria (Cadastrado Por, Atualizado Por), ocultos por padrão
                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('userUpdatedBy.name')
                    ->label('Atualizado Por')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtros para a tabela de histórico (opcionais, mas úteis)
                Tables\Filters\SelectFilter::make('tipo_evento')
                    ->label('Filtrar por Tipo de Evento')
                    ->options(TipoEventoHistorico::class),
                Tables\Filters\SelectFilter::make('prioridade')
                    ->label('Filtrar por Prioridade')
                    ->options(PrioridadeHistorico::class),
                Tables\Filters\SelectFilter::make('status_evento')
                    ->label('Filtrar por Status')
                    ->options(StatusEventoHistorico::class),
                Tables\Filters\TernaryFilter::make('afeta_disponibilidade')
                    ->label('Afeta Disponibilidade?')
                    ->placeholder('Ambos')
                    ->trueLabel('Sim')
                    ->falseLabel('Não')
                    ->nullable(),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(), // Botão para criar novo registro de histórico
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Ação de edição para cada registro
//                Tables\Actions\DeleteAction::make(), // Ação de exclusão
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(), // Ação de exclusão em massa
                ]),
            ])
            // ORDENAÇÃO: Ordena os registros do mais recente para o mais antigo (data de criação)
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Hook para preencher os campos cadastrado_por e atualizado_por ao criar um novo registro.
     * Isso é feito aqui no RelationManager para registros criados via o formulário em linha.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['cadastrado_por'] = Auth::id();
        $data['atualizado_por'] = Auth::id();
        return $data;
    }

    /**
     * Hook para preencher o campo atualizado_por ao atualizar um registro existente.
     * Isso é feito aqui no RelationManager para registros atualizados via o formulário em linha.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['atualizado_por'] = Auth::id();
        return $data;
    }
}
