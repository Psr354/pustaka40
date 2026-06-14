<?php

namespace App\Services;

use App\Models\Buku;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanService
{
    public function __construct(private readonly AuditLogService $auditLogService) {}

    /**
     * ANGGOTA: Request peminjaman (stok BELUM berkurang)
     */
    public function requestPeminjaman(array $payload): Peminjaman
    {
        return DB::transaction(function () use ($payload) {
            $buku = Buku::lockForUpdate()->findOrFail((int) $payload['buku_id']);
            $anggotaId = (int) $payload['anggota_id'];

            // Cek: apakah sudah request buku ini yang belum selesai?
            $sudahAda = Peminjaman::where('anggota_id', $anggotaId)
                ->where('buku_id', $buku->id)
                ->whereIn('status', ['menunggu_acc', 'disetujui', 'dipinjam'])
                ->exists();

            if ($sudahAda) {
                throw ValidationException::withMessages([
                    'buku_id' => 'Kamu sudah memiliki request/peminjaman aktif untuk buku ini.',
                ]);
            }

            // Buat record dengan status menunggu_acc
            $peminjaman = Peminjaman::create([
                'anggota_id' => $anggotaId,
                'buku_id' => $buku->id,
                'tgl_pinjam' => now(),
                'tgl_kembali_rencana' => $payload['tgl_kembali_rencana'],
                'status' => 'menunggu_acc',
                'denda' => 0,
            ]);

            $this->auditLogService->log('request', 'peminjaman', $peminjaman->id, null, $peminjaman->getAttributes());

            return $peminjaman;
        });
    }

    /**
     * ADMIN: Setujui request (stok BARU berkurang di sini)
     */
    public function approvePeminjaman(Peminjaman $peminjaman, int $adminId): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman, $adminId) {
            $pinjaman = Peminjaman::with('buku')->lockForUpdate()->findOrFail($peminjaman->id);

            if ($pinjaman->status !== 'menunggu_acc') {
                throw ValidationException::withMessages(['status' => 'Request ini sudah diproses.']);
            }

            if ($pinjaman->buku->stok < 1) {
                throw ValidationException::withMessages(['buku' => 'Stok buku habis, tidak bisa disetujui.']);
            }

            $dataLama = $pinjaman->getAttributes();

            // ✅ Stok berkurang HANYA saat di-approve
            $pinjaman->buku->decrement('stok');

            $pinjaman->update([
                'status' => 'dipinjam',
                'approved_by' => $adminId,
                'approved_at' => now(),
            ]);

            $this->auditLogService->log('approve', 'peminjaman', $pinjaman->id, $dataLama, $pinjaman->getAttributes());

            return $pinjaman->refresh();
        });
    }

    /**
     * ADMIN: Tolak request
     */
    public function rejectPeminjaman(Peminjaman $peminjaman, int $adminId, ?string $alasan = null): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman, $adminId, $alasan) {
            $pinjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

            if ($pinjaman->status !== 'menunggu_acc') {
                throw ValidationException::withMessages(['status' => 'Request sudah diproses.']);
            }

            $dataLama = $pinjaman->getAttributes();

            $pinjaman->update([
                'status' => 'ditolak',
                'approved_by' => $adminId,
                'approved_at' => now(),
                'catatan_denda' => $alasan,
            ]);

            $this->auditLogService->log('reject', 'peminjaman', $pinjaman->id, $dataLama, $pinjaman->getAttributes());

            return $pinjaman->refresh();
        });
    }

    /**
     * ADMIN: Proses pengembalian (sudah ada, kita pastikan tetap jalan)
     */
    public function kembalikan(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            $pinjaman = Peminjaman::with('buku')->lockForUpdate()->findOrFail($peminjaman->id);

            if ($pinjaman->status !== 'dipinjam') {
                throw ValidationException::withMessages(['status' => 'Peminjaman ini belum disetujui/dipinjam.']);
            }

            $tanggalAktual = Carbon::today();
            $tanggalRencana = Carbon::parse($pinjaman->tgl_kembali_rencana);
            $hariTerlambat = $tanggalAktual->greaterThan($tanggalRencana)
                ? $tanggalRencana->diffInDays($tanggalAktual)
                : 0;

            $dataLama = $pinjaman->getAttributes();

            $pinjaman->update([
                'tgl_kembali_aktual' => $tanggalAktual->toDateString(),
                'status' => 'dikembalikan',
                'denda' => $hariTerlambat * 1000,
            ]);

            $pinjaman->buku->increment('stok');

            $this->auditLogService->log('kembalikan', 'peminjaman', $pinjaman->id, $dataLama, $pinjaman->getAttributes());

            return $pinjaman->refresh();
        });
    }

    /**
     * ADMIN/ANGGOTA: Bayar denda (sudah ada)
     */
    public function bayarDenda(Peminjaman $peminjaman, int $jumlahBayar, ?string $catatan = null): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman, $jumlahBayar, $catatan) {
            $pinjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

            $sisa = max(0, $pinjaman->denda - $pinjaman->denda_dibayar);

            if ($sisa <= 0) throw ValidationException::withMessages(['jumlah_bayar' => 'Denda sudah lunas.']);
            if ($jumlahBayar > $sisa) throw ValidationException::withMessages(['jumlah_bayar' => 'Melebihi sisa denda.']);

            $dataLama = $pinjaman->getAttributes();

            $pinjaman->denda_dibayar += $jumlahBayar;
            $pinjaman->tgl_bayar_denda = now()->toDateString();
            $pinjaman->catatan_denda = $catatan;
            $pinjaman->save();

            $this->auditLogService->log('bayar_denda', 'peminjaman', $pinjaman->id, $dataLama, $pinjaman->getAttributes());

            return $pinjaman->refresh();
        });
    }
}
