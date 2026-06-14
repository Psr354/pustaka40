<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="dashboard-hero mb-4">
        <div>
            <h2 class="h5 mb-1">Selamat datang di Pustaka40</h2>
            <p class="text-muted mb-0">Ringkasan aktivitas perpustakaan dan status peminjaman terbaru.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('buku.create') }}" class="btn btn-primary">Tambah Buku</a>
            <a href="{{ route('peminjaman.create') }}" class="btn btn-outline-primary">Input Peminjaman</a>
        </div>
    </div>

    <div class="stat-grid mb-4">
        <div class="stat-card">
            <div class="stat-label">Total Buku</div>
            <div class="stat-value">{{ number_format($totalBuku) }}</div>
            <div class="stat-meta">Koleksi aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Anggota</div>
            <div class="stat-value">{{ number_format($totalAnggota) }}</div>
            <div class="stat-meta">Anggota terdaftar</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sedang Dipinjam</div>
            <div class="stat-value">{{ number_format($dipinjam) }}</div>
            <div class="stat-meta">Peminjaman aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Denda Bulan Ini</div>
            <div class="stat-value">Rp{{ number_format($totalDenda, 0, ',', '.') }}</div>
            <div class="stat-meta">Akumulasi denda</div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 mb-0">Buku Terpopuler</h3>
                        <a href="{{ route('buku.index') }}" class="btn btn-link btn-sm">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Judul</th>
                                    <th class="text-end">Dipinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bukuPopuler as $buku)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $buku->judul }}</div>
                                            <div class="small text-muted">{{ $buku->pengarang }}</div>
                                        </td>
                                        <td class="text-end">{{ number_format($buku->peminjaman_count) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Belum ada data peminjaman.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 mb-0">Peminjaman Terlambat</h3>
                        <a href="{{ route('peminjaman.index', ['status' => 'dipinjam']) }}" class="btn btn-link btn-sm">Tinjau</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Anggota</th>
                                    <th>Buku</th>
                                    <th class="text-end">Jatuh Tempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($terlambat as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->anggota?->nama ?? '-' }}</div>
                                            <div class="small text-muted">{{ $item->anggota?->nis ?? '-' }}</div>
                                        </td>
                                        <td>{{ $item->buku?->judul ?? '-' }}</td>
                                        <td class="text-end">{{ $item->tgl_kembali_rencana->format('d-m-Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada peminjaman terlambat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
