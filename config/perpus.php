<?php

return [
    // Batas maksimal buku yang bisa dipinjam aktif oleh 1 anggota.
    'max_pinjaman_aktif' => env('PERPUS_MAX_PINJAMAN_AKTIF', 3),

    // Lama peminjaman default (hari) saat anggota mengajukan.
    'durasi_default_hari' => env('PERPUS_DURASI_DEFAULT_HARI', 7),
];
