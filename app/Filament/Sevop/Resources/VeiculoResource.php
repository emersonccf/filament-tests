<?php

namespace App\Filament\Sevop\Resources;

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


// NOVOS IMPORTS NECESSÁRIOS:
use App\Models\Modelo; // Para a relação com o modelo
use App\Models\User;   // Para as relações de usuário (cadastrado_por, atualizado_por)
use App\Enums\DirecionamentoVeiculo; // Enums para os campos
use App\Enums\LocalAtivacaoVeiculo;
use App\Enums\CombustivelVeiculo;
use App\Enums\StatusVeiculo;
use Filament\Forms\Components\TextInput; // Garanta que este import está presente
use Filament\Forms\Components\Select;     // Para caixas de combinação
use Filament\Forms\Components\Placeholder; // Para exibir informações não editáveis (nome do usuário)
use Filament\Forms\Components\Hidden;     // Para ocultar campos preenchidos automaticamente
use Illuminate\Support\Facades\Auth;      // Para acessar o usuário logado
use Filament\Forms\Components\Section;    // Para organizar melhor o formulário

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 30; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações Básicas do Veículo') // Adiciona uma seção para organização
                ->schema([
                    Forms\Components\TextInput::make('placa')
                        ->maxLength(8)
                        ->required()
                        ->default(null)
                        ->unique(ignoreRecord: true) // Garante unicidade da placa ao editar
                        ->columnSpan(1) // Ocupa 1 coluna (seções usam 2 colunas por padrão)
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)), // <--- permite manipular o valor de um campo depois que o usuário o digitou e antes que ele seja processado e salvo.

                    // 1. Campo de Chave Estrangeira: id_modelo
                    // Substituímos o TextInput por um Select relacionado
                    Select::make('id_modelo')
                        ->label('Modelo do Veículo') // Rótulo amigável
                        ->relationship('modelo', 'nome_modelo') // Nome da relação no Model, e o campo a ser exibido
                        ->required()
                        ->searchable() // Permite pesquisar na lista de modelos
                        ->preload()    // Carrega todas as opções inicialmente para melhor UX em listas pequenas/médias
                        ->createOptionForm([ // Opcional: Permite criar um novo modelo diretamente aqui
                            // Campos mínimos para criar um Modelo
                            TextInput::make('nome_modelo')->required()->maxLength(50),
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
                            TextInput::make('numero_portas')->numeric()->default(0),
                        ])
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('prefixo_veiculo')
                        ->required()
                        ->maxLength(10)
                        ->columnSpan(1)
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),

                    // 2. Campos de Enum: Direcionamento, LocalAtivacao, Combustivel, Status
                    // Substituímos TextInputs por Selects usando os Enums
                    Select::make('direcionamento')
                        ->label('Direcionamento')
                        ->options(DirecionamentoVeiculo::class) // Usa o Enum para as opções
                        ->required()
                        ->searchable() // Permite pesquisar no Enum
                        ->native(false) // Renderiza um select personalizado do Filament
                        ->columnSpan(1),

                    Select::make('local_ativacao')
                        ->label('Local de Ativação')
                        ->options(LocalAtivacaoVeiculo::class)
                        ->required()
                        ->searchable()
                        ->native(false)
                        ->columnSpan(1),

                    Select::make('combustivel')
                        ->label('Tipo de Combustível')
                        ->options(CombustivelVeiculo::class)
                        ->required()
                        ->searchable()
                        ->native(false)
                        ->columnSpan(1),

                    Select::make('status')
                        ->label('Status Operacional')
                        ->options(StatusVeiculo::class)
                        ->required()
                        ->searchable()
                        ->native(false)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('possui_bateria_auxiliar') // Usando Toggle para booleanos
                    ->label('Possui Bateria Auxiliar?')
                        ->default(false)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('possui_gps') // Usando Toggle para booleanos
                    ->label('Possui GPS?')
                        ->default(false)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('quilometragem')
                        ->required()
                        ->numeric()
                        ->default(0.00)
                        ->step('0.01') // Permite decimais
                        ->columnSpan(1),

                    Forms\Components\DatePicker::make('data_recebimento')
                        ->label('Data de Recebimento')
                        ->nullable()
                        ->columnSpan(1),

                    Forms\Components\DatePicker::make('data_devolucao')
                        ->label('Data de Devolução')
                        ->nullable()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('chassi')
                        ->maxLength(17)
                        ->unique(ignoreRecord: true)
                        ->nullable()
                        ->columnSpan(1)
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),

                    Forms\Components\TextInput::make('renavam')
                        ->maxLength(11)
                        ->unique(ignoreRecord: true)
                        ->nullable()
                        ->columnSpan(1)
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),

                    Forms\Components\TextInput::make('ano_fabricacao')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(date('Y') + 1) // Ano atual + 1
                        ->nullable()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('ano_modelo')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(date('Y') + 1)
                        ->nullable()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('cor')
                        ->maxLength(30)
                        ->nullable()
                        ->columnSpan(1)
                        ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),

                    Forms\Components\TextInput::make('valor_diaria')
                        ->numeric()
                        ->prefix('R$') // Adiciona um prefixo
                        ->inputMode('decimal')
                        ->nullable()
                        ->columnSpan(1),
                ])->columns(2), // Define 2 colunas para esta seção

                Section::make('Informações de Auditoria') // Nova seção para campos de auditoria
                ->schema([
                    // 3. Campos de Auditoria: cadastrado_por e atualizado_por
                    // Oculta os campos para que o Observer possa preenchê-los sem interferência do usuário.
                    Hidden::make('cadastrado_por'),
                    Hidden::make('atualizado_por'),

                    // Placeholder para 'Cadastrado Por' - Exibe o nome do usuário, não editável.
                    Placeholder::make('cadastrado_por_display')
                        ->label('Cadastrado Por')
                        ->content(function (string $operation, ?Veiculo $record): string {
                            if ($operation === 'create') {
                                return Auth::user()->name; // Exibe o nome do usuário logado na criação
                            }
                            // Busca o nome do usuário através da relação no Model
                            return $record?->userCreatedBy?->name ?? 'N/A';
                        })
                        ->columnSpan(1),

                    // Placeholder para 'Atualizado Por' - Exibe o nome do usuário, não editável.
                    Placeholder::make('atualizado_por_display')
                        ->label('Atualizado Por')
                        ->content(function (string $operation, ?Veiculo $record): string {
                            if ($operation === 'create') {
                                return Auth::user()->name; // Exibe o nome do usuário logado na criação
                            }
                            // Busca o nome do usuário através da relação no Model
                            return $record?->userUpdatedBy?->name ?? 'N/A';
                        })
                        ->columnSpan(1),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->sortable()
                    ->searchable(),

                // Exibindo o nome do modelo em vez do ID
                Tables\Columns\TextColumn::make('modelo.nome_modelo')
                    ->label('Modelo') // Rótulo na tabela
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('prefixo_veiculo')
                    ->sortable()
                    ->searchable(),

                // Exibindo o label do Enum como um "badge"
                Tables\Columns\TextColumn::make('direcionamento')
                    ->searchable()
                    ->badge(), // Exibe o Enum como um badge usando o getLabel()

                Tables\Columns\TextColumn::make('local_ativacao')
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('combustivel')
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge(),

                Tables\Columns\ToggleColumn::make('possui_bateria_auxiliar') // Exibe como ícone (check/x)
                ->label('Bateria Aux.')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('possui_gps')
                    ->label('GPS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quilometragem')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_recebimento')
                    ->date()
                    ->dateTime('d-M-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_devolucao')
                    ->date()
                    ->dateTime('d-M-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('chassi')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculta por padrão para não sobrecarregar a tabela

                Tables\Columns\TextColumn::make('renavam')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ano_fabricacao')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ano_modelo')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('valor_diaria')
                    ->numeric()
                    ->money('BRL') // Formata como moeda brasileira
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Exibindo o nome do usuário em vez do ID
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
                // Adicionando filtros para as chaves estrangeiras e enums na tabela
                Tables\Filters\SelectFilter::make('id_modelo')
                    ->label('Filtrar por Modelo')
                    ->relationship('modelo', 'nome_modelo')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('direcionamento')
                    ->label('Filtrar por Direcionamento')
                    ->options(DirecionamentoVeiculo::class),

                Tables\Filters\SelectFilter::make('local_ativacao')
                    ->label('Filtrar por Local de Ativação')
                    ->options(LocalAtivacaoVeiculo::class),

                Tables\Filters\SelectFilter::make('combustivel')
                    ->label('Filtrar por Combustível')
                    ->options(CombustivelVeiculo::class),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options(StatusVeiculo::class),
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
            'index' => Pages\ListVeiculos::route('/'),
            'create' => Pages\CreateVeiculo::route('/create'),
            'edit' => Pages\EditVeiculo::route('/{record}/edit'),
        ];
    }
}
