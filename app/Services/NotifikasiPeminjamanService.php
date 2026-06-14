<?php

namespace App\Services;

use App\Models\NotifikasiPeminjaman;
use App\Models\Peminjaman;

class NotifikasiPeminjamanService
{
    public function generateHarian(): void
    {
        $today = now()->toDateString();
        $besok = now()->addDay()->toDateString();

        $pinjamanAktif = Peminjaman::query()
            ->with('anggota:id')
            ->where('status', 'dipinjam')
            ->get();

        foreach ($pinjamanAktif as $item) {
            if (! $item->anggota_id) {
                continue;
            }

            $tanggalRencana = $item->tgl_kembali_rencana?->toDateString();

            if ($tanggalRencana === null) {
                continue;
            }

            if ($tanggalRencana === $today || $tanggalRencana === $besok) {
                $this->createIfNotExists(
                    anggotaId: (int) $item->anggota_id,
                    peminjamanId: (int) $item->id,
                    jenis: 'jatuh_tempo',
                    pesan: 'Pengingat: peminjaman buku Anda mendekati jatuh tempo.',
                    tanggalNotifikasi: $today,
                );
            }

            if ($tanggalRencana < $today) {
                $this->createIfNotExists(
                    anggotaId: (int) $item->anggota_id,
                    peminjamanId: (int) $item->id,
                    jenis: 'terlambat',
                    pesan: 'Peringatan: peminjaman buku Anda sudah melewati jatuh tempo.',
                    tanggalNotifikasi: $today,
                );
            }
        }
    }

    public function markAsReadForAnggota(int $anggotaId): void
    {
        NotifikasiPeminjaman::query()
            ->where('anggota_id', $anggotaId)
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);
    }

    private function createIfNotExists(
        int $anggotaId,
        int $peminjamanId,
        string $jenis,
        string $pesan,
        string $tanggalNotifikasi
    ): void {
        NotifikasiPeminjaman::query()->firstOrCreate(
            [
                'anggota_id' => $anggotaId,
                'peminjaman_id' => $peminjamanId,
                'jenis' => $jenis,
                'tanggal_notifikasi' => $tanggalNotifikasi,
            ],
            [
                'pesan' => $pesan,
            ]
        );
    }
}

