<?php

namespace App\Models;

use App\Enums\TipoEventoHistorico; // Importar o Enum
use App\Enums\PrioridadeHistorico; // Importar o Enum
use App\Enums\StatusEventoHistorico; // Importar o Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoVeiculo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_historico';
    protected $table = 'historico_veiculos';

    protected $fillable = [
        'id_veiculo',
        'tipo_evento',
        'data_evento',
        'hora_evento',
        'quilometragem',
        'prioridade',
        'afeta_disponibilidade',
        'status_evento',
        'descricao',
        'local_ocorrencia',
        'prestador_servico',
        'data_prevista_conclusao',
        'data_conclusao',
        'observacoes',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'tipo_evento' => TipoEventoHistorico::class,
        'data_evento' => 'date',
        'hora_evento' => 'datetime', // 'time' cast can be tricky, 'datetime' is safer if you store full time
        'quilometragem' => 'decimal:2',
        'prioridade' => PrioridadeHistorico::class,
        'afeta_disponibilidade' => 'boolean',
        'status_evento' => StatusEventoHistorico::class,
        'data_prevista_conclusao' => 'date',
        'data_conclusao' => 'date',
    ];

    /**
     * Get the veiculo that owns the historico.
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo', 'id_veiculo');
    }

    /**
     * Get the user that registered the historico.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }

    /**
     * Get the user that last updated the historico.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por', 'id');
    }
}
