<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('facebook_page_id');
            $table->string('name');
            $table->text('access_token');
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'facebook_page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_pages');
    }
};
