<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';


    //Indica que campos se pueden añadir con Media::create(), si no estan Laravel los ignora.
    protected $fillable = [
        'pieza_id',
        'tipo',
        'path',
        'order',
        'es_portada',
        'mime_type',
        'size',
        'nombre_original'];

  public function pieza(){
      return $this->belongsTo(Pieza::class);
}



}
