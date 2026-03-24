<?php

use Carbon\Carbon;
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
        Schema::create('publicacion_red', function (Blueprint $table) {
            $table->foreignId('publicacion_id')->constrained('publicacions')->cascadeOnDelete();
            $table->foreignId('red_id')->constrained('reds')->cascadeOnDelete();
            $table->primary(['publicacion_id', 'red_id']);
            //Fecha para alerta(sin valor por defecto en BD.
            $table->date('fecha_vencimiento')->nullable();
            //Fecha de publicacion y actualización.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicacion_red');
    }
};
