<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel peminjaman.
     */
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->foreignId('buku_id')->constrained('buku')->cascadeOnDelete();
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali_rencana');
            $table->date('tgl_kembali_aktual')->nullable();
            $table->enum('status', ['menunggu_acc', 'dipinjam', 'dikembalikan', 'ditolak'])->default('menunggu_acc');
            $table->unsignedInteger('denda')->default(0);
            $table->timestamps();

            $table->index(['anggota_id', 'status']);
            $table->index(['buku_id', 'status']);
            $table->index('tgl_pinjam');
            $table->index('tgl_kembali_rencana');
        });
    }

    /**
     * Menghapus tabel peminjaman.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
