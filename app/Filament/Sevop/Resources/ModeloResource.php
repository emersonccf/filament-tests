<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\ModeloResource\Pages;
use App\Filament\Sevop\Resources\ModeloResource\RelationManagers;
use App\Models\Modelo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select; // Adicione este import para campos de seleção
use Filament\Forms\Components\TextInput; // Mantenha este para outros TextInputs
use Filament\Forms\Components\Placeholder; // Para exibir informações não editáveis
use Filament\Forms\Components\Hidden; // Para campos ocultos que são preenchidos por observers
use Illuminate\Support\Facades\Auth; // Para acessar o usuário logado no Placeholder

use App\Models\Marca; // Importar o modelo Marca para a relação
use App\Enums\CategoriaVeiculo; // Importar o Enum CategoriaVeiculo

class ModeloResource extends Resource
{
    protected static ?string $model = Modelo::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 20; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Campo de Chave Estrangeira: id_marca
                Select::make('id_marca')
                    ->label('Marca do Veículo') // Rótulo amigável
                    ->relationship('marca', 'nome_marca') // Nome da relação no Model, e o campo a ser exibido
                    ->required()
                    ->searchable() // Torna o campo pesquisável
                    ->preload() // Carrega todas as opções inicialmente para melhor UX em listas pequenas/médias
                    ->createOptionForm([ // Opcional: Permite criar uma nova marca diretamente no formulário de Modelo
                                         TextInput::make('nome_marca')
                                             ->required()
                                             ->maxLength(50),
                    ]),

                // 2. Campo de Enum: categoria
                Select::make('categoria')
                    ->label('Categoria do Veículo')
                    ->options(CategoriaVeiculo::class) // Filament 3.x suporta diretamente o Enum
                    ->required()
                    ->searchable() // Torna o campo pesquisável
                    ->native(false), // Opcional: Renderiza um select personalizado do Filament, não o nativo do navegador
                // O valor padrão 'OUTROS' é definido no seu Model via $casts se o Enum tiver um caso padrão,
                // ou você pode definir aqui com ->default(CategoriaVeiculo::OUTROS), mas o cast já deve lidar com isso.

                // Outros campos de texto para Modelo (mantidos como TextInput)
                TextInput::make('nome_modelo')
                    ->required()
                    ->maxLength(50)
                    ->dehydrateStateUsing(fn (string $state): string => mb_strtoupper($state)),
                TextInput::make('numero_portas')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('capacidade_passageiros')
                    ->required()
                    ->numeric()
                    ->default(2),
                TextInput::make('numero_rodas')
                    ->required()
                    ->numeric()
                    ->default(4),
                TextInput::make('cilindrada')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('peso_bruto') // Use TextInput para campos numéricos
                ->numeric() // Garante que apenas números podem ser digitados
                ->step('0.01')
                ->default(null),
                // Opcional: Você pode adicionar ->step('0.01') se precisar de um seletor de passo no campo,
                // mas a validação de decimais é mais controlada pelo tipo da coluna no banco de dados.

                // 3. Campos de Auditoria: cadastrado_por e atualizado_por
                // Oculta os campos para que o Observer possa preenchê-los sem interferência do usuário.
                Hidden::make('cadastrado_por'),
                Hidden::make('atualizado_por'),

                // Placeholder para 'Cadastrado Por' - Exibe o nome do usuário, não editável.
                Placeholder::make('cadastrado_por_display')
                    ->label('Cadastrado Por')
                    ->content(function (string $operation, ?Modelo $record): string {
                        if ($operation === 'create') {
                            return Auth::user()->name; // Exibe o nome do usuário logado na criação
                        }
                        // Busca o nome do usuário através da relação no Model
                        return $record?->userCreatedBy?->name ?? 'N/A';
                    })
                    ->columnSpan(1), // Ajuste o layout conforme necessário

                // Placeholder para 'Atualizado Por' - Exibe o nome do usuário, não editável.
                Placeholder::make('atualizado_por_display')
                    ->label('Atualizado Por')
                    ->content(function (string $operation, ?Modelo $record): string {
                        if ($operation === 'create') {
                            return Auth::user()->name; // Exibe o nome do usuário logado na criação
                        }
                        // Busca o nome do usuário através da relação no Model
                        return $record?->userUpdatedBy?->name ?? 'N/A';
                    })
                    ->columnSpan(1), // Ajuste o layout conforme necessário
            ]);
    }

    // Modificação da Função `table()`
    // Isso garante que os nomes dos usuários sejam exibidos nas colunas da tabela
    // em vez dos IDs, e que as relações de chave estrangeira sejam bem formatadas.
    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 10]) // Limita para APENAS 5 a 10 registros por página
            ->columns([
                Tables\Columns\TextColumn::make('marca.nome_marca') // Exibe o nome da marca
                ->label('Marca')
                    ->sortable()
                    ->searchable(), // Adicione searchable para a coluna de marca

                Tables\Columns\TextColumn::make('nome_modelo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('categoria')
                    ->searchable()
                    ->badge(), // Exibe o Enum como um badge, usando o getLabel() do Enum

                Tables\Columns\TextColumn::make('numero_portas')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacidade_passageiros')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('numero_rodas')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cilindrada')
                    ->searchable(),

                Tables\Columns\TextColumn::make('peso_bruto')
                    ->numeric()
                    ->sortable(),

                // Colunas para cadastrado_por e atualizado_por (nomes dos usuários)
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
                // Filtros podem ser adicionados aqui, por exemplo:
                SelectFilter::make('id_marca')
                    ->label('Filtrar por Marca')
                    ->relationship('marca', 'nome_marca')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categoria')
                    ->label('Filtrar por Categoria')
                    ->options(CategoriaVeiculo::class),
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
            'index' => Pages\ListModelos::route('/'),
            'create' => Pages\CreateModelo::route('/create'),
            'edit' => Pages\EditModelo::route('/{record}/edit'),
        ];
    }
}
