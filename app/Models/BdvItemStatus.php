<?php

namespace App\Models;

use App\Enums\TipoRegistroStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BdvItemStatus extends Model
{
    use HasFactory;

    protected $table = 'bdv_item_status';
    protected $primaryKey = 'id_item_status';

    protected $fillable = [
        'id_registro_motorista',
        'tipo_registro',
        'crlv',
        'lacre_placa',
        'oleo_freio',
        'oleo_motor',
        'pneus_estado',
        'retrovisor_direito_esquerdo',
        'buzina',
        'luzes_farol_alto_baixo_estacionamento',
        'luzes_pisca_re_freios',
        'chaparia_pintura',
        'giroflex',
        'sirene',
        'velocimetro',
        'bancos_estado',
        'bateria_agua',
        'paralamas_dianteiro_traseiro',
        'descarga_completa',
        'etiqueta_revisao',
        'tampas_laterais',
        'protetor_perna',
        'fechadura_chave',
        'carenagem_tanque',
        'carenagem_farol',
        'tanque_estrutura',
        'caixa_lado_esq_lado_dir',
        'punhos_manete',
        'macaco',
        'chave_roda',
        'triangulo',
        'estepe',
        'extintor',
        'agua_radiador',
        'calotas',
        'retrovisor_interno',
        'macanetas_fechaduras',
        'limpadores',
        'luzes_internas',
        'cinto_seguranca',
        'radio_am_fm',
        'estofamento',
        'cadastrado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'tipo_registro' => TipoRegistroStatusEnum::class,
        // Todos os campos booleanos devem ser castados para 'boolean'
        'crlv' => 'boolean',
        'lacre_placa' => 'boolean',
        'oleo_freio' => 'boolean',
        'oleo_motor' => 'boolean',
        'pneus_estado' => 'boolean',
        'retrovisor_direito_esquerdo' => 'boolean',
        'buzina' => 'boolean',
        'luzes_farol_alto_baixo_estacionamento' => 'boolean',
        'luzes_pisca_re_freios' => 'boolean',
        'chaparia_pintura' => 'boolean',
        'giroflex' => 'boolean',
        'sirene' => 'boolean',
        'velocimetro' => 'boolean',
        'bancos_estado' => 'boolean',
        'bateria_agua' => 'boolean',
        'paralamas_dianteiro_traseiro' => 'boolean',
        'descarga_completa' => 'boolean',
        'etiqueta_revisao' => 'boolean',
        'tampas_laterais' => 'boolean',
        'protetor_perna' => 'boolean',
        'fechadura_chave' => 'boolean',
        'carenagem_tanque' => 'boolean',
        'carenagem_farol' => 'boolean',
        'tanque_estrutura' => 'boolean',
        'caixa_lado_esq_lado_dir' => 'boolean',
        'punhos_manete' => 'boolean',
        'macaco' => 'boolean',
        'chave_roda' => 'boolean',
        'triangulo' => 'boolean',
        'estepe' => 'boolean',
        'extintor' => 'boolean',
        'agua_radiador' => 'boolean',
        'calotas' => 'boolean',
        'retrovisor_interno' => 'boolean',
        'macanetas_fechaduras' => 'boolean',
        'limpadores' => 'boolean',
        'luzes_internas' => 'boolean',
        'cinto_seguranca' => 'boolean',
        'radio_am_fm' => 'boolean',
        'estofamento' => 'boolean',
    ];

    /**
     * Get the driver registration record that owns this item status.
     */
    public function registroMotorista(): BelongsTo
    {
        return $this->belongsTo(BdvRegistroMotorista::class, 'id_registro_motorista', 'id_registro_motorista');
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
