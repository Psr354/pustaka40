<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('menunggu_acc', 'disetujui', 'dipinjam', 'dikembalikan', 'terlambat', 'ditolak') DEFAULT 'menunggu_acc'");
        }

        if (! Schema::hasColumn('peminjaman', 'approved_by')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                $table->timestamp('approved_at')->nullable()->after('approved_by');

                if (DB::getDriverName() === 'mysql') {
                    $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('peminjaman', 'approved_by')) {
            Schema::table('peminjaman', function (Blueprint $table) {
                if (DB::getDriverName() === 'mysql') {
                    $table->dropForeign(['approved_by']);
                }

                $table->dropColumn(['approved_by', 'approved_at']);
            });
        }
    }
};
