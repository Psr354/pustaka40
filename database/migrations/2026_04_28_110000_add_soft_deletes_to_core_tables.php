<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('buku', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

