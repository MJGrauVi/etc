<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{

    /** @use HasFactory<\Database\Factories\PerfilFactory> */
    use HasFactory;

    //Para que Laravel sepa el nombre real de mi tabla y no aplique perfiles.
    protected $table = 'perfils';

    protected $fillable = [
        'user_id',
        'tipo_documento',
        'documento',
        'movil',
        'logo',
        'descripcion',
        'web',
        'redes_sociales'
    ];
    protected $casts = [
        'redes_sociales' => 'array',
    ];

    protected $appends = ['logo_url'];

    // Para que no falle el factory.:
    //protected $hidden = ['logo_url'];
    Public function getLogoUrlAttribute(){
        //Si hay logo devuelve la url completa, sino null.
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
