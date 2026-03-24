<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfils', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_documento', ['nif', 'cif', 'nie'])->nullable();
            //Introduce nif, cif o nie.
            $table->string('documento')->unique()->nullable();
            //Adicional al telefono del usuario.
            $table->string('movil')->nullable();
            //Imagen del logo.
            $table->string('logo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('web')->nullable();
            $table->json('redes_sociales')->nullable();
            $table->timestamps();
            //Relación 1:1 con users.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }
    public function down(): void{
        Schema::dropIfExists('perfils');
    }
};
