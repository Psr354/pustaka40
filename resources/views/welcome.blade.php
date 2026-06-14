<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Pustaka40') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
    <header class="landing-nav">
        <a href="{{ route('home') }}" class="landing-brand">Pustaka<span>40</span></a>

        <nav class="landing-actions" aria-label="Navigasi utama">
            <a href="#koleksi" class="landing-link">Koleksi</a>
            <a href="#alur" class="landing-link">Alur</a>
            <a href="{{ route('password.request') }}" class="landing-link">Lupa password</a>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @endif
        </nav>
    </header>

    <main>
        <section class="landing-hero">
            <div class="landing-hero-copy">
                <div class="hero-kicker">Untuk anggota perpustakaan</div>
                <h1 class="hero-title">Cari buku dulu. Login nanti saat kamu benar-benar mau pinjam.</h1>
                <p class="hero-lead">
                    Pustaka40 dibuat supaya kamu tidak nebak-nebak stok, kategori, atau status peminjaman. Buka koleksi, lihat detail buku, lalu ajukan peminjaman dari satu tempat.
                </p>

                <div class="landing-cta">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Masuk dan Cari Buku</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Buat Akun Anggota</a>
                    @endif
                </div>

                <div class="landing-trust">
                    <div>
                        <strong>Cek stok</strong>
                        <span>Tahu buku tersedia sebelum mengajukan</span>
                    </div>
                    <div>
                        <strong>Lihat detail</strong>
                        <span>Judul, pengarang, genre, deskripsi</span>
                    </div>
                    <div>
                        <strong>Pantau status</strong>
                        <span>Menunggu ACC, dipinjam, atau kembali</span>
                    </div>
                </div>
            </div>

            <div class="landing-showcase" aria-label="Cuplikan dashboard Pustaka40">
                <div class="showcase-topbar">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="showcase-header">
                    <div>
                        <div class="showcase-eyebrow">Yang kamu cari</div>
                        <h2>Koleksi Buku</h2>
                    </div>
                    <div class="showcase-pill">Siap dipinjam</div>
                </div>

                <div class="showcase-search">Cari buku: Laravel, Sejarah, Novel</div>

                <div class="showcase-list">
                    <div class="showcase-book active">
                        <div class="book-mark"></div>
                        <div>
                            <strong>Laravel in Action</strong>
                            <span>Teknologi - 4 stok tersedia</span>
                        </div>
                        <em>Detail</em>
                    </div>
                    <div class="showcase-book">
                        <div class="book-mark book-mark-gold"></div>
                        <div>
                            <strong>Sejarah Nusantara</strong>
                            <span>Nonfiksi - Ada deskripsi</span>
                        </div>
                        <em>Baca</em>
                    </div>
                    <div class="showcase-book">
                        <div class="book-mark book-mark-cyan"></div>
                        <div>
                            <strong>Legenda Kota Tua</strong>
                            <span>Novel - Bisa diajukan</span>
                        </div>
                        <em>Ajukan</em>
                    </div>
                </div>
            </div>
        </section>

        <section id="koleksi" class="landing-band">
            <div class="section-heading">
                <span>Yang terasa buat pengguna</span>
                <h2>Halaman ini menjawab pertanyaan yang biasanya muncul sebelum meminjam buku.</h2>
            </div>

            <div class="feature-grid">
                <article class="feature-tile">
                    <div class="feature-icon">01</div>
                    <h3>Bukunya ada atau tidak?</h3>
                    <p>Stok buku terlihat jelas, jadi kamu tidak perlu bertanya manual hanya untuk memastikan buku masih tersedia.</p>
                </article>
                <article class="feature-tile">
                    <div class="feature-icon">02</div>
                    <h3>Ini buku tentang apa?</h3>
                    <p>Detail buku menampilkan pengarang, tahun terbit, kategori, genre, dan deskripsi kalau sudah tersedia.</p>
                </article>
                <article class="feature-tile">
                    <div class="feature-icon">03</div>
                    <h3>Pengajuan sampai mana?</h3>
                    <p>Status peminjaman bisa dipantau setelah login: menunggu ACC, dipinjam, terlambat, atau sudah dikembalikan.</p>
                </article>
            </div>
        </section>

        <section id="alur" class="landing-flow">
            <div class="section-heading">
                <span>Cara pakainya</span>
                <h2>Alurnya dibuat pendek supaya anggota tidak tersesat.</h2>
            </div>

            <div class="flow-steps">
                <div class="flow-step">
                    <strong>1</strong>
                    <span>Masuk atau daftar</span>
                    <p>Gunakan akun anggota untuk membuka koleksi dan mengajukan peminjaman.</p>
                </div>
                <div class="flow-step">
                    <strong>2</strong>
                    <span>Pilih buku</span>
                    <p>Cek detail, stok, kategori, dan genre sebelum menekan tombol ajukan.</p>
                </div>
                <div class="flow-step">
                    <strong>3</strong>
                    <span>Tunggu ACC admin</span>
                    <p>Pengajuan masuk ke admin, lalu statusnya bisa kamu pantau dari menu peminjaman.</p>
                </div>
            </div>
        </section>

        <section class="landing-help">
            <div>
                <span>Sudah punya akun tapi tidak bisa masuk?</span>
                <h2>Pulihkan password tanpa harus membuat akun baru.</h2>
                <p>Masukkan email terdaftar, lalu gunakan link reset yang dikirim sistem. Kalau email belum masuk, cek juga folder spam.</p>
            </div>
            <a href="{{ route('password.request') }}" class="btn btn-primary">Reset Password</a>
        </section>
    </main>
</body>
</html>
