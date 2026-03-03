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
            $table->string('nombre_completo');
            $table->string('avatar');
            $table->text('descripcion');
            $table->foreignId('user_id')->unique()->constrained();
        });
    }
};
