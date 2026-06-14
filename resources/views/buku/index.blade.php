<x-app-layout>
    <x-slot name="header">
        {{ auth()->user()->isAdmin() ? 'Manajemen Buku' : 'Katalog Buku' }}
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1">Daftar Buku</h2>
                    <p class="text-muted small mb-0">Klik detail untuk melihat cover, deskripsi, kategori, genre, dan ketersediaan buku.</p>
                </div>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('buku.create') }}" class="btn btn-primary btn-sm">Tambah Buku</a>
                @endif
            </div>

            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Judul / Pengarang / Kategori">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach ($daftarKategori as $kategori)
                            <option value="{{ $kategori->id }}" @selected(request('kategori_id') == $kategori->id)>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Cover</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Kategori</th>
                            <th>Tahun Terbit</th>
                            <th>Stok</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarBuku as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($daftarBuku->currentPage() - 1) * $daftarBuku->perPage() }}</td>
                                <td>
                                    <a href="{{ route('buku.show', $item) }}" class="book-cover-thumb" aria-label="Lihat detail {{ $item->judul }}">
                                        @if ($item->cover_url)
                                            <img src="{{ $item->cover_url }}" alt="Cover {{ $item->judul }}">
                                        @else
                                            <span>{{ Str::of($item->judul)->substr(0, 1)->upper() }}</span>
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('buku.show', $item) }}" class="fw-semibold text-decoration-none">
                                        {{ $item->judul }}
                                    </a>
                                    @if (blank($item->deskripsi))
                                        <div class="small text-warning">Deskripsi belum diisi</div>
                                    @else
                                        <div class="small text-muted">{{ Str::limit($item->deskripsi, 80) }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->pengarang }}</td>
                                <td>
                                    @if ($item->kategori->isEmpty())
                                        <span class="text-muted">-</span>
                                    @else
                                        @foreach ($item->kategori as $kategori)
                                            <span class="badge bg-secondary">{{ $kategori->nama_kategori }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $item->tahun_terbit }}</td>
                                <td>{{ $item->stok }}</td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('buku.show', $item) }}" class="btn btn-info btn-sm">Detail</a>
                                    @if (auth()->user()->role === 'admin')
                                        <a href="{{ route('buku.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>

                                        <form action="{{ route('buku.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data buku ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data buku.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarBuku->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarBuku->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
