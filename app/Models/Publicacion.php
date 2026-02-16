<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Publicacion extends Model
{
    /** @use HasFactory<\Database\Factories\PublicacionFactory> */
    use HasFactory;

    public function publicaciones():BelongsToMany{
        return $this->belongsToMany(Media::class)->withPivot('fecha_vencimiento')->withTimestamps();
    }
}
