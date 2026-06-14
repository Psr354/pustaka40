<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genre')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['buku_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_genre');
    }
};
