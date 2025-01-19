<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cpf',
        'email',
        'password',
        'is_active',
        'is_admin',
        'belongs_sector',
    ];

    public $timestamps = false;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
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

    public function canAccessPanel(Panel $panel): bool
    {
        # TODO: Criar uma regra que retorne verdadeiro para que o usuário possa ser autorizado a ter acesso a area administrativa
//        return $this->is_active && $this->is_admin && $this->hasVerifiedEmail();
//        return str_ends_with($this->email, '@admin.com') && $this->hasVerifiedEmail();
        return true;
    }

}
