<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedInteger('denda_dibayar')->default(0)->after('denda');
            $table->date('tgl_bayar_denda')->nullable()->after('denda_dibayar');
            $table->string('catatan_denda', 255)->nullable()->after('tgl_bayar_denda');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['denda_dibayar', 'tgl_bayar_denda', 'catatan_denda']);
        });
    }
};

