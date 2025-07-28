<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BdvMain extends Model
{
    use HasFactory;

    protected $table = 'bdv_main'; // Nome da tabela no banco
    protected $primaryKey = 'id_bdv'; // Chave primária da tabela

    protected $fillable = [
        'id_veiculo',
        'data_referencia',
        'observacoes_gerais',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'data_referencia' => 'date',
    ];

    /**
     * Get the vehicle that owns the BDV main record.
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo', 'id_veiculo');
    }

    /**
     * Get the driver registration records for the BDV main record.
     */
    public function registrosMotoristas(): HasMany
    {
        return $this->hasMany(BdvRegistroMotorista::class, 'id_bdv', 'id_bdv');
    }
    /**
     * Get the user who created the BDV main record.
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por', 'id');
    }
    /**
     * Get the user who updated the BDV main record.
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por');
    }

}
