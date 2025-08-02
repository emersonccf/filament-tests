<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Marca extends Model
{
    use HasFactory, HasRoles;

    protected $primaryKey = 'id_marca';
    protected $table = 'marcas';

    protected $fillable = [
        'nome_marca',
        'cadastrado_por',
        'atualizado_por',
    ];

    /**
     * Get the modelos for the marca.
     */
    public function modelos(): HasMany
    {
        return $this->hasMany(Modelo::class, 'id_marca', 'id_marca');
    }

    /**
     * Get the user that registered the marca.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }

    /**
     * Get the user that last updated the marca.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por', 'id');
    }
}

