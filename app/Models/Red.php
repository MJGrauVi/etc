<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Red extends Model
{
    /** @use HasFactory<\Database\Factories\RedFactory> */
    use HasFactory;
    protected $table = 'reds';

    protected $fillable = [
        'nombre',
        'url_base'

    ];
    public function publicacions (): BelongsToMany {
        return $this->belongsToMany(Publicacion::class);
    }
}
