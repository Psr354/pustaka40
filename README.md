# 📚 Pustaka40 — Sistem Manajemen Perpustakaan Digital

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![License](https://img.shields.io/badge/license-MIT-blue)
![Version](https://img.shields.io/badge/version-1.0.0-orange)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4)

Pustaka40 adalah aplikasi manajemen perpustakaan berbasis Laravel untuk membantu admin mengelola buku, anggota, peminjaman, denda, dan pengguna dalam satu dashboard. Aplikasi ini juga menyediakan katalog buku untuk anggota, detail buku dengan cover, pengajuan peminjaman, notifikasi email, serta fitur profil modern termasuk foto profil dari galeri maupun kamera.

## ✨ Fitur Utama

- 🔐 Autentikasi user dengan role `admin` dan `anggota`.
- 📖 Manajemen buku lengkap dengan cover, deskripsi, kategori, genre, stok, dan halaman detail.
- 🧑‍🎓 Manajemen anggota dan relasi akun user.
- 🔄 Alur peminjaman: pengajuan anggota, ACC/tolak admin, pengembalian, status terlambat, dan arsip data.
- 💰 Perhitungan denda keterlambatan aktif dan pembayaran denda.
- 📩 Forgot password dan email notifikasi peminjaman.
- 🧾 Export laporan peminjaman ke CSV, Excel-compatible CSV, dan PDF.
- 🧑‍💼 Manajemen profil dengan upload foto dari galeri dan kamera desktop/mobile.
- 🧩 Audit log untuk aktivitas penting seperti create, update, restore, dan delete.
- 🎨 Landing page, dashboard responsif, dan UI Bootstrap 5.

## 🛠 Tech Stack

- **Backend:** PHP 8.2+, Laravel 11
- **Frontend:** Blade, Bootstrap 5, Vite
- **Database:** MySQL/MariaDB
- **Auth:** Laravel Breeze
- **PDF Export:** barryvdh/laravel-dompdf
- **Testing:** PHPUnit
- **Tooling:** Composer, NPM, Laravel Pint

## 📋 Prasyarat

Pastikan environment lokal sudah memiliki:

- PHP `8.2+`
- Composer `2.x`
- Node.js `18+` dan NPM
- MySQL atau MariaDB
- Web server lokal seperti Laragon, XAMPP, Laravel Herd, atau `php artisan serve`
- Ekstensi PHP umum Laravel: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`

## 🚀 Instalasi & Setup

Clone repository:

```bash
git clone https://github.com/Psr354/pustaka40.git
cd pustaka40
```

Install dependency backend:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

Siapkan file environment:

```bash
cp .env.example .env
php artisan key:generate
```

Atur koneksi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpus_laravel
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Buat symbolic link untuk file upload:

```bash
php artisan storage:link
```

Build asset frontend:

```bash
npm run build
```

Jalankan aplikasi:

```bash
php artisan serve
```

Akses aplikasi di:

```text
http://127.0.0.1:8000
```

## ▶️ Cara Penggunaan

Login menggunakan akun hasil seeder, atau buat akun baru melalui halaman register jika fitur registrasi aktif.

```text
Email: admin@perpus.test
Password: password
```

Alur penggunaan utama:

1. Admin login ke dashboard.
2. Admin menambahkan kategori, genre, buku, dan cover buku.
3. Anggota membuka katalog buku dan melihat detail buku.
4. Anggota mengajukan peminjaman dari halaman detail buku.
5. Admin menyetujui atau menolak pengajuan.
6. Saat buku dikembalikan, sistem menghitung denda jika terlambat.
7. Admin dapat export laporan peminjaman dalam format CSV atau PDF.

Placeholder screenshot:

```md
![Dashboard Screenshot](https://raw.githubusercontent.com/Psr354/pustaka40/main/docs/screenshots/dashboard.png)
![Book Detail Screenshot](https://raw.githubusercontent.com/Psr354/pustaka40/main/docs/screenshots/book-detail.png)
```

## 📁 Struktur Folder

```text
.
├── app/
│   ├── Http/Controllers/     # Controller aplikasi
│   ├── Http/Requests/        # Form request dan validasi
│   ├── Mail/                 # Email notification
│   ├── Models/               # Model Eloquent
│   └── Services/             # Business logic service
├── database/
│   ├── migrations/           # Struktur database
│   ├── seeders/              # Data awal aplikasi
│   └── factories/            # Factory testing
├── resources/
│   ├── css/                  # Styling aplikasi
│   ├── js/                   # JavaScript frontend
│   └── views/                # Blade templates
├── routes/
│   ├── web.php               # Route web
│   └── console.php           # Command Artisan custom
├── storage/                  # File upload, cache, dan log
└── tests/                    # Unit dan feature tests
```

## 🧪 Testing

Jalankan seluruh test:

```bash
php artisan test
```

Jalankan test spesifik:

```bash
php artisan test --filter=ProfileTest
php artisan test --filter=PeminjamanBusinessRulesTest
```

## 🤝 Contributing

Kontribusi sangat terbuka. Untuk menjaga kualitas kode, gunakan alur berikut:

1. Fork repository ini.
2. Buat branch fitur:

   ```bash
   git checkout -b feature/nama-fitur
   ```

3. Lakukan perubahan secara fokus dan kecil.
4. Jalankan test dan build:

   ```bash
   php artisan test
   npm run build
   ```

5. Commit perubahan dengan pesan yang jelas:

   ```bash
   git commit -m "feat: add nama fitur"
   ```

6. Push branch dan buat Pull Request.

## 📄 Lisensi

Project ini menggunakan lisensi **MIT**. Silakan gunakan, modifikasi, dan distribusikan sesuai kebutuhan dengan tetap mengikuti ketentuan lisensi.

## 📬 Kontak

Maintainer:

- **Nama:** Azzam Azhim Muntazhar
- **Email:** azzamazhimmuntazhar@gmail.com
- **GitHub:** `https://github.com/psr354`

Jika repository ini digunakan untuk kebutuhan sekolah, kampus, atau portofolio, sesuaikan nama maintainer dan URL GitHub sebelum dipublikasikan.
