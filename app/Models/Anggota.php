<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anggota';

    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'kelas',
        'no_hp',
        'alamat',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many Anggota -> Peminjaman.
     */
    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Alias relasi untuk kompatibilitas kode existing.
     */
    public function peminjaman(): HasMany
    {
        return $this->peminjamans();
    }
}
