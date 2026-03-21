<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;
    protected $table = 'medias';

    //Indica que campos se pueden añadir con Media::create(), si no estan Laravel los ignora.
    protected $fillable = [
        'pieza_id',
        'nombre_original',
        'tipo',
        'path',
        'es_portada'];

  public function piezas():BelongsTo{
      return $this->belongsTo(Pieza::class);
}
protected static function booted(){
      static::deleting(function($media){
          Storage::disk('public')->delete($media->path);
      });
}
// En el modelo Media.php

    protected $appends = ['url_completa'];

    public function getUrlCompletaAttribute()
    {
        // Esto convierte 'imagenes/ducha.jpg' en 'http://localhost/storage/imagenes/ducha.jpg'
        return asset('storage/' . $this->path);
    }

}
