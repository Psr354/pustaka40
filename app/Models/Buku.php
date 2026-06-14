<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buku';

    protected $fillable = [
        'judul',
        'pengarang',
        'deskripsi',
        'cover_path',
        'tahun_terbit',
        'stok',
    ];

    /**
     * Relasi many-to-many Buku <-> Kategori.
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(Kategori::class, 'buku_kategori');
    }

    /**
     * Alias relasi untuk kompatibilitas kode existing.
     */
    public function kategori(): BelongsToMany
    {
        return $this->kategoris();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'buku_genre');
    }

    public function genre(): BelongsToMany
    {
        return $this->genres();
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? asset('storage/' . $this->cover_path) : null;
    }

    /**
     * Relasi one-to-many Buku -> Peminjaman.
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
