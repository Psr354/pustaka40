<x-app-layout>
    <x-slot name="header">
        Manajemen Anggota
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <h2 class="h5 mb-0">Daftar Anggota</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('anggota.index', ['trashed' => 'only']) }}" class="btn btn-outline-dark btn-sm">Arsip</a>
                    <a href="{{ route('anggota.index') }}" class="btn btn-outline-secondary btn-sm">Data Aktif</a>
                    <a href="{{ route('anggota.create') }}" class="btn btn-primary btn-sm">Tambah Anggota</a>
                </div>
            </div>

            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-5">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="NIS atau nama anggota">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Kelas</label>
                    <input type="text" name="kelas" value="{{ request('kelas') }}" class="form-control" placeholder="Contoh: XI RPL 1">
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                    <a href="{{ route('anggota.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Akun User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarAnggota as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($daftarAnggota->currentPage() - 1) * $daftarAnggota->perPage() }}</td>
                                <td>{{ $item->nis }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->kelas }}</td>
                                <td>
                                    @if ($item->user)
                                        <span class="fw-semibold">{{ $item->user->name }}</span>
                                        <div class="small text-muted">{{ $item->user->email }}</div>
                                    @else
                                        <span class="text-muted">Belum ditautkan</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <div class="action-wrap">
                                    @if (!empty($isArchive))
                                        <form action="{{ route('anggota.restore', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pulihkan anggota ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Pulihkan</button>
                                        </form>
                                    @else
                                        <a href="{{ route('anggota.show', $item) }}" class="btn btn-info btn-sm">Detail</a>
                                        <a href="{{ route('anggota.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('anggota.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
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
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data anggota.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarAnggota->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarAnggota->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
