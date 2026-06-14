<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peminjaman';

    protected $fillable = [
        'anggota_id',
        'buku_id',
        'tgl_pinjam',
        'tgl_kembali_rencana',
        'tgl_kembali_aktual',
        'status',
        'denda',
        'denda_dibayar',
        'tgl_bayar_denda',
        'catatan_denda',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tgl_pinjam' => 'date',
        'tgl_kembali_rencana' => 'date',
        'tgl_kembali_aktual' => 'date',
        'tgl_bayar_denda' => 'date',
        'approved_at' => 'datetime',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /**
     * Relasi belongs-to Peminjaman -> Buku.
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function getHariTerlambatAttribute(): int
    {
        if (! $this->tgl_kembali_rencana || $this->status === 'ditolak') {
            return 0;
        }

        $tanggalAcuan = $this->tgl_kembali_aktual ?? now();

        return $tanggalAcuan->greaterThan($this->tgl_kembali_rencana)
            ? (int) $this->tgl_kembali_rencana->diffInDays($tanggalAcuan)
            : 0;
    }

    public function getTotalDendaAttribute(): int
    {
        return max((int) $this->denda, $this->hari_terlambat * 1000);
    }

    public function getSisaDendaAttribute(): int
    {
        return max(0, $this->total_denda - (int) $this->denda_dibayar);
    }
}
