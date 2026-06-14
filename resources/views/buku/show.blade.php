<x-app-layout>
    <x-slot name="header">
        Detail Buku
    </x-slot>

    @php
        $deskripsiBuku = trim((string) $buku->deskripsi);
        $kategoriKosong = $buku->kategori->isEmpty();
        $genreKosong = $buku->genre->isEmpty();
        $dataBelumLengkap = collect([
            $buku->cover_path ? null : 'cover',
            $deskripsiBuku === '' ? 'deskripsi' : null,
            $kategoriKosong ? 'kategori' : null,
            $genreKosong ? 'genre' : null,
        ])->filter();
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
        <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary btn-sm align-self-start">Kembali ke Daftar Buku</a>

        <div class="d-flex flex-wrap gap-2">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('buku.edit', $buku) }}" class="btn btn-warning btn-sm">Edit Buku</a>
            @endif

            @if (auth()->user()->isAnggota() && auth()->user()->anggota)
                <a href="{{ route('peminjaman.ajukan.form', ['buku_id' => $buku->id]) }}"
                   class="btn btn-primary btn-sm {{ $buku->stok < 1 ? 'disabled' : '' }}"
                   @if ($buku->stok < 1) aria-disabled="true" tabindex="-1" @endif>
                    {{ $buku->stok < 1 ? 'Tidak Tersedia' : 'Ajukan Peminjaman' }}
                </a>
            @endif
        </div>
    </div>

    @if ($dataBelumLengkap->isNotEmpty())
        <div class="alert alert-warning">
            Data buku ini belum lengkap: {{ $dataBelumLengkap->join(', ') }}.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row gap-3 mb-3">
                        <div class="book-cover-detail">
                            @if ($buku->cover_url)
                                <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}">
                            @else
                                <span>{{ Str::of($buku->judul)->substr(0, 1)->upper() }}</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <div>
                                    <h2 class="h4 mb-1">{{ $buku->judul }}</h2>
                                    <div class="text-muted">oleh {{ $buku->pengarang }}</div>
                                </div>
                                <div>
                                    <span class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $buku->stok > 0 ? 'Tersedia' : 'Stok Habis' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="h6">Deskripsi</h3>
                    @if ($deskripsiBuku === '')
                        <p class="text-muted mb-0">Belum ada deskripsi untuk buku ini.</p>
                    @else
                        <p class="mb-0">{{ $deskripsiBuku }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h6 mb-3">Informasi Buku</h3>
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Tahun Terbit</dt>
                        <dd class="col-7">{{ $buku->tahun_terbit }}</dd>

                        <dt class="col-5 text-muted">Stok</dt>
                        <dd class="col-7">{{ $buku->stok }}</dd>

                        <dt class="col-5 text-muted">Sedang Dipinjam</dt>
                        <dd class="col-7">{{ $jumlahDipinjam }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 mb-3">Kategori & Genre</h3>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">Kategori</div>
                        @forelse ($buku->kategori as $kategori)
                            <span class="badge bg-secondary">{{ $kategori->nama_kategori }}</span>
                        @empty
                            <span class="text-muted">Belum diisi</span>
                        @endforelse
                    </div>

                    <div>
                        <div class="text-muted small mb-1">Genre</div>
                        @forelse ($buku->genre as $genre)
                            <span class="badge bg-dark-subtle text-dark border">{{ $genre->nama_genre }}</span>
                        @empty
                            <span class="text-muted">Belum diisi</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
