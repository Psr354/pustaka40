<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel pivot buku_kategori.
     */
    public function up(): void
    {
        Schema::create('buku_kategori', function (Blueprint $table) {
            $table->foreignId('buku_id')->constrained('buku')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();

            $table->primary(['buku_id', 'kategori_id']);
        });
    }

    /**
     * Menghapus tabel pivot buku_kategori.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_kategori');
    }
};
