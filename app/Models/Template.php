<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'preview_path',
        'config_json',
        'active',
    ];

    protected $casts = [
        'config_json' => 'array',   // convierte JSONB ↔ array automáticamente
        'active' => 'boolean',      // convierte 0/1 ↔ true/false
    ];
}
