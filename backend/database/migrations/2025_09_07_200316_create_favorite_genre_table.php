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
        Schema::create('favorite_genre', function (Blueprint $table) {
            $table->unsignedBigInteger('favorite_id');
            $table->unsignedBigInteger('genre_id');

            $table->foreign('favorite_id')
                ->references('id')->on('favorites')
                ->onDelete('cascade');

            $table->foreign('genre_id')
                ->references('id')->on('genres')
                ->onDelete('cascade');

            $table->primary(['favorite_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_genre');
    }
};
