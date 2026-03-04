<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{

    /** @use HasFactory<\Database\Factories\PerfilFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre_usuario',
        'dni_cif',
        'avatar',
        'descripcion',
    ];
}
