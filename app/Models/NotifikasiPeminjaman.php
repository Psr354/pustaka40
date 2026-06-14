<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_peminjaman';

    protected $fillable = [
        'anggota_id',
        'peminjaman_id',
        'jenis',
        'pesan',
        'tanggal_notifikasi',
        'dibaca_pada',
    ];

    protected $casts = [
        'tanggal_notifikasi' => 'date',
        'dibaca_pada' => 'datetime',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }
}

