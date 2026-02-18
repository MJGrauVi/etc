<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            /*Relación co pieza*/
            /*Si borro la pieza se borran las imagenes(medias) relacionadas con ella.*/
            $table->foreignId('pieza_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['image', 'video'])->nullable();
            /*Ruta dentro del disco de Laravel(public, s3, etc.*/
            $table->string('path');
            /*Orden y portada*/
            $table->integer('order')->default(0);
            $table->boolean('es_portada')->default(false);
            /*Información técnica del archivo.*/
            $table->string('mime_type')->nullable();//ej: image/jpeg,png...
            $table->unsignedBigInteger('size')->nullable();//Tamaño en bytes.
            $table->string('nombre_original')->nullable();//Nombre original del archivo.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
