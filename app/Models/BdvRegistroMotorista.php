<?php

namespace App\Models;

use App\Enums\NivelCombustivelEnum;
use App\Enums\TipoTurnoEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Decimal;

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
        'km_chegada' => 'decimal:2',
        'nivel_combustivel_saida' => NivelCombustivelEnum::class,
        'momento_chegada' => 'datetime',
        'km_chegada' => 'decimal:2',
        'nivel_combustivel_chegada' => NivelCombustivelEnum::class,
    ];

    protected $appends = [
        'quilometragem_rodada',
        'titulo_bdv',
    ];

    /**
     * Retorna a quilometragem rodada pelo veículo ou nulo
     *
     * @return null|float
     */
    public function getQuilometragemRodadaAttribute(): ?float
    {
        if($this->km_saida === null or $this->km_chegada === null)
            return null;

        return $this->km_chegada - $this->km_saida;
    }
    /**
     * Retorna o título para o BDV -- Título personalizado para Modais do BDV
     *
     * @return string
     */
    public function getTituloBdvAttribute(): string
    {
        $bdv = str_pad($this->bdvMain->id_bdv, 5, '0', STR_PAD_LEFT);
        return "- BDV-{$bdv} - {$this->condutor->nome} - {$this->bdvMain->veiculo->placa} - {$this->bdvMain->veiculo->prefixo_veiculo}";
    }
    /**
     * Calcula as horas em atuação (diferença entre momento_chegada e momento_saida)
     *
     * @return float|null Retorna as horas em formato decimal ou null se inválido
     */
    public function getHorasEmAtuacaoAttribute(): ?float
    {
        // Verifica se ambos os campos estão preenchidos
        if ($this->momento_saida === null || $this->momento_chegada === null) {
            return null;
        }

        try {
            // Converte para objetos Carbon
            $momentoSaida = Carbon::parse($this->momento_saida);
            $momentoChegada = Carbon::parse($this->momento_chegada);

            // Validação: momento_chegada deve ser posterior ao momento_saida
            if ($momentoChegada->lessThanOrEqualTo($momentoSaida)) {
                return null;
            }

            // Calcula a diferença em horas com precisão decimal
            $diferencaEmMinutos = $momentoChegada->diffInMinutes($momentoSaida);

            // Converte para horas e arredonda para 2 casas decimais
            return abs(round($diferencaEmMinutos / 60, 2));

        } catch (\Exception $e) {
            Log::error('Erro ao calcular horas em atuação', [
                'id_registro' => $this->id ?? 'novo',
                'momento_saida' => $this->momento_saida,
                'momento_chegada' => $this->momento_chegada,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
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
