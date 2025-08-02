<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\Enums\SexoEnum;
use App\Enums\EstadoCivilEnum;
use App\Enums\TipoSanguineoEnum;


class Pessoa extends Model implements Auditable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasUuids, \OwenIt\Auditing\Auditable;

    protected $dates = ['deleted_at'];

    protected $primaryKey = 'rus_id';

//    public $timestamps = false;

    protected $fillable = [
        'rus_id',
        'uuid_id',
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

    protected function casts(): array
    {
        return [
            'uuid_id' => 'string',
        ];
    }

    protected function uuidId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value ?? (string) Str::uuid(),
        );
    }

    public function uniqueIds(): array
    {
        return ['uuid_id'];
    }

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

    /**
     * Get the alocacoes for the veiculo.
     */
    public function historicoVeiculos(): HasMany
    {
        return $this->hasMany(HistoricoVeiculo::class, 'id_pessoa', 'rus_id');
    }


    /**
     * Get the BDV registration records where this person is the driver.
     */
    public function bdvRegistrosComoCondutor(): HasMany
    {
        return $this->hasMany(BdvRegistroMotorista::class, 'id_condutor', 'rus_id');
    }

    /**
     * Get the BDV registration records where this person is the departure supervisor.
     */
    public function bdvRegistrosComoEncarregadoSaida(): HasMany
    {
        return $this->hasMany(BdvRegistroMotorista::class, 'id_encarregado_saida', 'rus_id');
    }

    /**
     * Get the BDV registration records where this person is the arrival supervisor.
     */
    public function bdvRegistrosComoEncarregadoChegada(): HasMany
    {
        return $this->hasMany(BdvRegistroMotorista::class, 'id_encarregado_chegada', 'rus_id');
    }
}
