<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'genre';

    protected $fillable = [
        'nama_genre',
        'deskripsi',
    ];

    public function bukus(): BelongsToMany
    {
        return $this->belongsToMany(Buku::class, 'buku_genre');
    }

    public function buku(): BelongsToMany
    {
        return $this->bukus();
    }
}
