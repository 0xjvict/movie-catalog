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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id');
            $table->unsignedBigInteger('user_id');

            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->date('release_date')->nullable();
            $table->string('origin_country', 10)->nullable();

            $table->integer('runtime_minutes')->nullable();
            $table->string('tagline')->nullable();
            $table->text('overview')->nullable();

            $table->decimal('vote_average', 3, 1)->default(0);
            $table->unsignedInteger('vote_count')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'tmdb_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
