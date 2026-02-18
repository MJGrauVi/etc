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
        'user_id',
        'nombre',
        'descripcion',
        'precio',
        ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function media():HasMany{
        return $this->hasMany(Media::class);
    }

}
