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

    /*Un usuario puede tener muchas piezas
    Una pieza puede tener muchas publicaciones
    Una pieza puede tener muchas imágenes
    Una publicación pertenece a una sola pieza*/

    protected $fillable = [
        'pieza_id',
        'nombre',
        'descripcion',
        'publicado_en',
        ];

    public function piezas():BelongsTo{
        return $this->belongsTo(Pieza::class);
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

}
