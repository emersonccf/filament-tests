<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Unidade extends Model
{
    use HasFactory, HasRoles;

    protected $primaryKey = 'id_unidade';
    protected $table = 'unidades';

    protected $fillable = [
        'nome_unidade',
        'codigo_unidade',
        'telefone',
        'responsavel',
        'cadastrado_por',
        'atualizado_por',
    ];

    /**
     * Get the alocacoes for the unidade.
     */
    public function alocacoes(): HasMany
    {
        return $this->hasMany(AlocacaoVeiculo::class, 'id_unidade', 'id_unidade');
    }

    /**
     * Get the user that registered the unidade.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }

    /**
     * Get the user that last updated the unidade.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por', 'id');
    }
}
