<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Relasi many-to-many Kategori <-> Buku.
     */
    public function bukus(): BelongsToMany
    {
        return $this->belongsToMany(Buku::class, 'buku_kategori');
    }

    /**
     * Alias relasi untuk kompatibilitas kode existing.
     */
    public function buku(): BelongsToMany
    {
        return $this->bukus();
    }
}
