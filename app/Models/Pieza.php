<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pieza extends Model
{
    /** @use HasFactory<\Database\Factories\PiezaFactory> */
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'precio',
        ];

    public function users(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function medias():HasMany{
        return $this->hasMany(Media::class);
    }
    public function publicacions():HasMany{
        return $this->hasMany(Publicacion::class);
    }

}
