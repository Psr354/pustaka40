<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel kategori.
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Menghapus tabel kategori.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
