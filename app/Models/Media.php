<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;
    protected $table = 'medias';

    //Indica que campos se pueden añadir con Media::create(), si no estan Laravel los ignora.
    protected $fillable = [
        'tipo',
        'path',
        'order',
        'es_portada'];

  public function piezas():BelongsTo{
      return $this->belongsTo(Pieza::class);
}
protected static function booted(){
      static::deleting(function($media){
          Storage::disk('public')->delete($media->path);
      });
}
public function getUrlAttribute(){
      return asset('storage/' . $this->path);
}

}
