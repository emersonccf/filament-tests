<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasProfilePhoto;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasUuids, HasProfilePhoto;

    // Indica que o UUID não é a chave primária.
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid_id',
        'name',
        'cpf',
        'email',
        'profile_photo_path',
        'password',
        'is_active', //TODO: Campos devem sair daqui...
        'is_admin',
        'belongs_sector',
        'change_password',
    ];

//    public $timestamps = false;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        /*
         * Para evitar acúmulo de arquivos não utilizados, você pode implementar um método para limpar fotos antigas quando uma nova for carregada
         * */
        static::updating(function ($user) {
            if ($user->isDirty('profile_photo_path') && $user->getOriginal('profile_photo_path')) {
                Storage::delete($user->getOriginal('profile_photo_path'));
            }
        });
    }

    protected function profilePhotoDisk() : string
    {
        return 'public';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid_id'];
    }

    /**
     * Set the user's CPF.
     *
     * @param  string  $value
     * @return void
     */
    public function setCpfAttribute($value)
    {
        // Remove todos os caracteres que não são números
        $this->attributes['cpf'] = preg_replace('/\D/', '', $value);
    }

    /**
     * Get the formatted CPF.
     *
     * @return string
     */
    public function getCpfAttribute($value)
    {
        // Retorna o CPF formatado com máscara
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $value);
    }

    /**
     * Get the user's profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute() : string
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        return $this->defaultProfilePhotoUrl();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        # TODO: Criar uma regra que retorne verdadeiro para que o usuário possa ser autorizado a ter acesso a area administrativa
//        return $this->is_active && $this->is_admin && $this->hasVerifiedEmail();
//        return str_ends_with($this->email, '@admin.com') && $this->hasVerifiedEmail();
        return true;
    }

}
