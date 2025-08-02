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
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class Veiculo extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, HasRoles;

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
        'km_proxima_revisao',
        'revisao_pendente',
        'localidade_ativacao_mat',
        'localidade_ativacao_vesp',
        'localidade_ativacao_not',
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
    protected $appends = ['placa_modelo_direcionamento',
                          'veiculo_em_dias_com_revisao'
    ];

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
    public function historicoVeiculos(): HasMany
    {
        return $this->hasMany(HistoricoVeiculo::class, 'id_veiculo', 'id_veiculo');
    }
    /**
     * Get the BDV main records for the vehicle.
     */
    public function bdvMainRecords(): HasMany
    {
        return $this->hasMany(BdvMain::class, 'id_veiculo', 'id_veiculo');
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
    /**
     * Retorna true se tudo ok com a revisão
     *
     * @return boolean
     */
    public function getVeiculoEmDiasComRevisaoAttribute(): bool
    {
        // Verifica se km_proxima_revisao não é null para evitar erros
        if ($this->km_proxima_revisao === null) {
            return false;
        }

        return $this->quilometragem <= $this->km_proxima_revisao;
    }
    /**
     * Retorna a quantidade de rodas de um determinado modelo de veículo
     *
     * @return null|int
     */
    public function getNumeroRodasAttribute(): ?int
    {
        return $this->modelo?->numero_rodas;
    }
}
