<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorauthenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Pieza;
use App\Notifications\VerifyEmailCustom;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    //use Notifiable activa los métodos $user->notify() y $user->notifications.
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    //Campos que podemos insertar con create().
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'password',
        'rol',
        'perfil'


    ];

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
     * Sobrescribimos la notificación de verificación de email para APIs.
     * Laravel, por defecto, redirige a la ruta 'login' (solo válida en proyectos web).
     * Esta implementación genera un enlace API y evita la redirección que causaba
     * el error "Route [login] not defined".
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom);
    }
    //Relacion 1:1, un usuario tiene un perfil

    public function piezas(): HasMany{
        return $this->hasMany(Pieza::class);
    }
    //Añadimos la relacion con perfil.
    public function perfil(): HasOne{
        return $this->hasOne(Perfil::class);
    }
    public function facebookPages(): HasMany{
        return $this->hasMany(FacebookPage::class);
    }
    public function defaultFacebookPage(): HasOne{
        return $this->hasOne(FacebookPage::class)->where('is_default', true);
    }
    public function hasRole(string $rol): bool
    {
        return $this->roles()->where('name', $rol)->exists();
    }


}
