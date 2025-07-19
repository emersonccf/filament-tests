<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlocacaoVeiculo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_alocacao';
    protected $table = 'alocacao_veiculos';

    protected $fillable = [
        'id_veiculo',
        'id_unidade',
        'data_inicio',
        'data_fim',
        'observacoes',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /**
     * Get the veiculo that owns the alocacao.
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo', 'id_veiculo');
    }

    /**
     * Get the unidade that owns the alocacao.
     */
    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class, 'id_unidade', 'id_unidade');
    }

    /**
     * Get the user that registered the alocacao.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }

    /**
     * Get the user that last updated the alocacao.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por', 'id');
    }
}

