<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publicacion_red', function (Blueprint $table) {
            $table->string('estado_publicacion')->nullable()->after('fecha_vencimiento');
            $table->string('imagen_publicada_path')->nullable()->after('estado_publicacion');
            $table->string('external_photo_id')->nullable()->after('imagen_publicada_path');
            $table->string('external_post_id')->nullable()->after('external_photo_id');
            $table->timestamp('published_at')->nullable()->after('external_post_id');
            $table->text('error')->nullable()->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('publicacion_red', function (Blueprint $table) {
            $table->dropColumn([
                'estado_publicacion',
                'imagen_publicada_path',
                'external_photo_id',
                'external_post_id',
                'published_at',
                'error',
            ]);
        });
    }
};
