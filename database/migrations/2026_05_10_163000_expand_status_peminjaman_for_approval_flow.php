<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('menunggu_acc','dipinjam','dikembalikan','ditolak') NOT NULL DEFAULT 'menunggu_acc'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam'");
        }
    }
};
