<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->enum('jenis', ['jatuh_tempo', 'terlambat']);
            $table->string('pesan', 255);
            $table->date('tanggal_notifikasi');
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->unique(['peminjaman_id', 'jenis', 'tanggal_notifikasi'], 'uniq_notif_harian');
            $table->index(['anggota_id', 'dibaca_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_peminjaman');
    }
};

