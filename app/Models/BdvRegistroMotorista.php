<?php

namespace App\Models;

use App\Enums\NivelCombustivelEnum;
use App\Enums\TipoTurnoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BdvRegistroMotorista extends Model
{
    use HasFactory;

    protected $table = 'bdv_registro_motorista';
    protected $primaryKey = 'id_registro_motorista';

    protected $fillable = [
        'id_bdv',
        'id_condutor',
        'tipo_turno',
        'momento_saida',
        'km_saida',
        'nivel_combustivel_saida',
        'observacoes_saida',
        'id_encarregado_saida',
        'momento_chegada',
        'km_chegada',
        'nivel_combustivel_chegada',
        'observacoes_chegada',
        'id_encarregado_chegada',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'tipo_turno' => TipoTurnoEnum::class,
        'momento_saida' => 'datetime',
        'km_saida' => 'decimal:2',
        'nivel_combustivel_saida' => NivelCombustivelEnum::class,
        'momento_chegada' => 'datetime',
        'km_chegada' => 'decimal:2',
        'nivel_combustivel_chegada' => NivelCombustivelEnum::class,
    ];

    /**
     * Get the main BDV record that owns this registration.
     */
    public function bdvMain(): BelongsTo
    {
        return $this->belongsTo(BdvMain::class, 'id_bdv', 'id_bdv');
    }

    /**
     * Get the person who is the driver for this registration.
     */
    public function condutor(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'id_condutor', 'rus_id');
    }

    /**
     * Get the person who supervised the departure.
     */
    public function encarregadoSaida(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'id_encarregado_saida', 'rus_id');
    }

    /**
     * Get the person who supervised the arrival.
     */
    public function encarregadoChegada(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'id_encarregado_chegada', 'rus_id');
    }

    /**
     * Get the item status records for this driver registration (Saida and Chegada).
     */
    public function itemStatus(): HasMany
    {
        return $this->hasMany(BdvItemStatus::class, 'id_registro_motorista', 'id_registro_motorista');
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
