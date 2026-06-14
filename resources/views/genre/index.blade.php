<x-app-layout>
    <x-slot name="header">
        Manajemen Genre
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <h2 class="h5 mb-0">Daftar Genre</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('genre.index', ['trashed' => 'only']) }}" class="btn btn-outline-dark btn-sm">Arsip</a>
                    <a href="{{ route('genre.index') }}" class="btn btn-outline-secondary btn-sm">Data Aktif</a>
                    <a href="{{ route('genre.create') }}" class="btn btn-primary btn-sm">Tambah Genre</a>
                </div>
            </div>

            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama / deskripsi genre">
                </div>
                <div class="col-12 col-md-6">
                    <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                    <a href="{{ route('genre.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Genre</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarGenre as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($daftarGenre->currentPage() - 1) * $daftarGenre->perPage() }}</td>
                                <td>{{ $item->nama_genre }}</td>
                                <td>{{ $item->deskripsi ?: '-' }}</td>
                                <td>{{ $item->bukus_count }}</td>
                                <td class="text-nowrap">
                                    <div class="action-wrap">
                                    @if (!empty($isArchive))
                                        <form action="{{ route('genre.restore', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pulihkan genre ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Pulihkan</button>
                                        </form>
                                    @else
                                        <a href="{{ route('genre.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('genre.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus genre ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Arsipkan</button>
                                        </form>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data genre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarGenre->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarGenre->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
