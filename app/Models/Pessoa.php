<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\SexoEnum;
use App\Enums\EstadoCivilEnum;
use App\Enums\TipoSanguineoEnum;


class Pessoa extends Model
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'rus_id';


    protected $fillable = [
        'matricula',
        'registro_unico',
        'foto',
        'nome',
        'sexo',
        'data_nascimento',
        'tipo_sanguineo',
        'estado_civil',
        'possui_filhos',
        'cpf',
        'rg',
        'rg_orgao_emissor',
        'whats_app',
        'tel_01',
        'tel_02',
        'email',
        'observacoes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pessoa) {
            if (empty($pessoa->rus_id)) {
                // Definir um valor temporário apenas para a criação
                $pessoa->rus_id = 0;
            }
        });
    }

    public function setSexoAttribute($value)
    {
        $this->attributes['sexo'] = $value instanceof SexoEnum ? $value : SexoEnum::from($value);
    }

    public function getSexoAttribute($value): SexoEnum
    {
        return SexoEnum::from($value);
    }

    public function setTipoSanguineoAttribute($value)
    {
        $this->attributes['tipo_sanguineo'] = $value instanceof TipoSanguineoEnum ? $value : TipoSanguineoEnum::from($value);
    }

    public function getTipoSanguineoAttribute($value): TipoSanguineoEnum
    {
        return TipoSanguineoEnum::from($value);
    }

    public function setEstadoCivilAttribute($value)
    {
        $this->attributes['estado_civil'] = $value instanceof EstadoCivilEnum ? $value : EstadoCivilEnum::from($value);
    }

    public function getEstadoCivilAttribute($value): EstadoCivilEnum
    {
        return EstadoCivilEnum::from($value);
    }
}
