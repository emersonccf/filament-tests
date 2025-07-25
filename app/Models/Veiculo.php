<?php

namespace App\Models;

use App\Enums\DirecionamentoVeiculo; // Importar o Enum
use App\Enums\LocalAtivacaoVeiculo; // Importar o Enum
use App\Enums\CombustivelVeiculo; // Importar o Enum
use App\Enums\StatusVeiculo; // Importar o Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veiculo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_veiculo';
    protected $table = 'veiculos';

    protected $fillable = [
        'placa',
        'id_modelo',
        'prefixo_veiculo',
        'direcionamento',
        'local_ativacao',
        'combustivel',
        'status',
        'km_proxima_revisao', //add - falta colocar na tabela
        'revisao_pendente', //add
        'localidade_ativacao_mat', //add
        'localidade_ativacao_vesp', //add
        'localidade_ativacao_not', //add
        'possui_bateria_auxiliar',
        'possui_gps',
        'quilometragem',
        'data_recebimento',
        'data_devolucao',
        'chassi',
        'renavam',
        'ano_fabricacao',
        'ano_modelo',
        'cor',
        'valor_diaria',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'direcionamento' => DirecionamentoVeiculo::class,
        'local_ativacao' => LocalAtivacaoVeiculo::class,
        'combustivel' => CombustivelVeiculo::class,
        'status' => StatusVeiculo::class,
        'data_recebimento' => 'date',
        'valor_diaria' => 'decimal:2',
    ];

    /**
     * Adiciona 'placa_modelo_direcionamento' à lista de atributos que devem ser anexados
     * ao array/JSON do modelo.
     * Isso garante que o atributo calculado esteja disponível ao serializar o modelo.
     */
    protected $appends = ['placa_modelo_direcionamento'];

    /**
     * Get the modelo that owns the veiculo.
     */
    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class, 'id_modelo', 'id_modelo');
    }

    /**
     * Get the alocacoes for the veiculo.
     */
    public function alocacoes(): HasMany
    {
        return $this->hasMany(AlocacaoVeiculo::class, 'id_veiculo', 'id_veiculo');
    }

    /**
     * Get the historicos for the veiculo.
     */
    public function historicos(): HasMany
    {
        return $this->hasMany(HistoricoVeiculo::class, 'id_veiculo', 'id_veiculo');
    }

    /**
     * Get the user that registered the veiculo.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }

    /**
     * Get the user that last updated the veiculo.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por', 'id');
    }

    /**
     * Get the formatted placa, modelo, and direcionamento for the veiculo.
     *
     * @return string
     */
    public function getPlacaModeloDirecionamentoAttribute(): string
    {
        // Certifica-se de que o relacionamento 'modelo' está carregado para evitar N+1 queries.
        // Se o modelo não estiver carregado, ele será carregado aqui.
        $modeloNome = $this->modelo->nome_modelo ?? 'N/A';

        return "{$this->placa} / {$modeloNome} / {$this->direcionamento->value}";
    }
}
