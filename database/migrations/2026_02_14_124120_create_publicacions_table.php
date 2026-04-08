<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('publicacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pieza_id')->constrained()->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->text('contenido')->nullable();//Ira el contenido generado por IA.
            $table->enum('estado',['borrador', 'pendiente', 'publicado', 'error'])->default('borrador');
            $table->string('hashtags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacions');
    }
};
