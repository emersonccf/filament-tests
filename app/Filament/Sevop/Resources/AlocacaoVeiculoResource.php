<?php

namespace App\Filament\Sevop\Resources;

use App\Filament\Sevop\Resources\AlocacaoVeiculoResource\Pages;
use App\Filament\Sevop\Resources\AlocacaoVeiculoResource\RelationManagers;
use App\Models\AlocacaoVeiculo;
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
use App\Models\Unidade;   // Para a relação com Unidade
use App\Models\User;     // Para as relações de usuário (cadastrado_por, atualizado_por)
use Filament\Forms\Components\Select;     // Para caixas de combinação
use Filament\Forms\Components\Placeholder; // Para exibir informações não editáveis
use Filament\Forms\Components\Hidden;     // Para ocultar campos preenchidos automaticamente
use Illuminate\Support\Facades\Auth;      // Para acessar o usuário logado
use Filament\Forms\Components\Section;    // Para organizar melhor o formulário


class AlocacaoVeiculoResource extends Resource
{
    protected static ?string $model = AlocacaoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Controle de Frota'; // <--- Adicione esta linha
    protected static ?int $navigationSort = 50; // <--- Adicione esta linha para ordenar dentro do grupo

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalhes da Alocação')
                    ->schema([
                        // Campo de Chave Estrangeira: id_veiculo
                        Select::make('id_veiculo')
                            ->label('Veículo (Placa)')
                            ->relationship('veiculo', 'placa') // Exibe a placa do veículo
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1), // Ocupa 1 coluna

                        // Campo de Chave Estrangeira: id_unidade
                        Select::make('id_unidade')
                            ->label('Unidade (Nome)')
                            ->relationship('unidade', 'nome_unidade') // Exibe o nome da unidade
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_inicio')
                            ->label('Data de Início')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('data_fim')
                            ->label('Data de Fim')
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('observacoes')
                            ->label('Observações')
                            ->columnSpanFull()
                            ->maxLength(65535), // Se for text na migration, pode ter um limite
                    ])->columns(2), // Define 2 colunas para esta seção

                Section::make('Informações de Auditoria')
                    ->schema([
                        Hidden::make('cadastrado_por'),
                        Hidden::make('atualizado_por'),
                        Placeholder::make('cadastrado_por_display')
                            ->label('Cadastrado Por')
                            ->content(function (string $operation, ?AlocacaoVeiculo $record): string {
                                if ($operation === 'create') {
                                    return Auth::user()->name;
                                }
                                return $record?->userCreatedBy?->name ?? 'N/A';
                            })
                            ->columnSpan(1),
                        Placeholder::make('atualizado_por_display')
                            ->label('Atualizado Por')
                            ->content(function (string $operation, ?AlocacaoVeiculo $record): string {
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
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Veículo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unidade.nome_unidade')
                    ->label('Unidade')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_inicio')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_fim')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('observacoes')
                    ->wrap() // Quebra linha para textos longos
                    ->limit(50), // Limita a exibição na tabela
                Tables\Columns\TextColumn::make('userCreatedBy.name')
                    ->label('Cadastrado Por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('userUpdatedBy.name')
                    ->label('Atualizado Por')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_veiculo')
                    ->label('Filtrar por Veículo')
                    ->relationship('veiculo', 'placa')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('id_unidade')
                    ->label('Filtrar por Unidade')
                    ->relationship('unidade', 'nome_unidade')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('data_inicio')
                    ->form([
                        Forms\Components\DatePicker::make('data_inicio_from')
                            ->label('Data de Início (De)'),
                        Forms\Components\DatePicker::make('data_inicio_until')
                            ->label('Data de Início (Até)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '>=', $date),
                            )
                            ->when(
                                $data['data_inicio_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_inicio', '<=', $date),
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
            'index' => Pages\ListAlocacaoVeiculos::route('/'),
            'create' => Pages\CreateAlocacaoVeiculo::route('/create'),
            'edit' => Pages\EditAlocacaoVeiculo::route('/{record}/edit'),
        ];
    }
}
