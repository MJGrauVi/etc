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

    protected $table = 'publicacions';

    /*Un usuario puede tener muchas piezas
    Una pieza puede tener muchas publicaciones
    Una pieza puede tener muchas imágenes
    Una publicación pertenece a una sola pieza*/

    protected $fillable = [
        'titulo',
        'contenido',
        'estado',
        'hashtags',
        'pieza_id',
        'user_id'
        ];

    public function piezas():BelongsTo{
        return $this->belongsTo(Pieza::class, 'pieza_id');
    }
    public function medias():HasManyThrough{
        return $this->hasManyThrough(
            Media::class,
            Pieza::class,
            'id',
            'pieza_id',
            'pieza_id',
            'id'
        );
    }
    protected $casts = [
        'hashtags' => 'array',
    ];
    public function reds():BelongsToMany{
        return $this->belongsToMany(Red::class, 'publicacion_red')->withPivot('fecha_vencimiento')->withTimestamps();
    }

    public function publicar()
    {
        if ($this->estado !== 'borrador') {
            throw new \Exception('Solo se puede publicar desde estado borrador.');
        }

        $this->update([
            'estado' => 'publicado'


        ]);
    }
    public function users()
    {
        // Una publicación PERTENECE a un usuario
        return $this->belongsTo(User::class);
    }

}
