# Outline Presentasi (7-10 Menit)

## 1. Pembuka (1 menit)

- Tujuan aplikasi: digitalisasi proses perpustakaan sekolah.
- Teknologi yang dipakai: Laravel 11, MySQL, Bootstrap, DomPDF.

## 2. Desain Database (2-3 menit)

- Jelaskan ERD dari [ERD.md](C:/laragon/www/perpus_laravel/docs/ERD.md).
- Alasan pemisahan `users` dan `anggota`.
- Alasan penggunaan pivot `buku_kategori`.
- Alasan kolom operasional denda di tabel `peminjaman`.

## 3. Demo Fitur Utama (3-4 menit)

1. Login sebagai admin.
2. CRUD buku/kategori/anggota.
3. Buat peminjaman, lalu kembalikan untuk lihat denda otomatis.
4. Bayar denda.
5. Tunjukkan role anggota (akses terbatas).
6. Tunjukkan dashboard statistik.

## 4. Bonus (1 menit)

- Export laporan peminjaman ke PDF (DomPDF).
- Email notifikasi saat peminjaman dibuat/disetujui.

## 5. Penutup dan Q&A (1 menit)

- Ringkas keputusan desain utama.
- Siapkan jawaban:
  - kenapa pakai soft delete,
  - bagaimana validasi stok dan batas pinjaman,
  - bagaimana proteksi route berbasis role.
