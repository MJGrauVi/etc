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
            $table->foreignId('pieza_id')->constrained()->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('estado',['borrador', 'pendiente', 'publicado', 'error'])->default('borrador');
            $table->enum('publicado_en',['facebook', 'instagram', 'tiktok', 'x'])->nullable();
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
