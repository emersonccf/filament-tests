<?php

namespace App\Models;

use App\Enums\CategoriaVeiculo; // Importar o Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modelo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_modelo';
    protected $table = 'modelos';

    protected $fillable = [
        'id_marca',
        'nome_modelo',
        'categoria',
        'numero_portas',
        'capacidade_passageiros',
        'numero_rodas',
        'cilindrada',
        'peso_bruto',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'categoria' => CategoriaVeiculo::class, // Cast para o Enum
    ];

    /**
     * Get the marca that owns the modelo.
     */
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
    }

    /**
     * Get the veiculos for the modelo.
     */
    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class, 'id_modelo', 'id_modelo');
    }

    /**
     * Get the user that registered the modelo.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }

    /**
     * Get the user that last updated the modelo.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por', 'id');
    }
}

