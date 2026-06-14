<x-app-layout>
    <x-slot name="header">
        Manajemen User
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <h2 class="h5 mb-0">Daftar User</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('user.index', ['trashed' => 'only']) }}" class="btn btn-outline-dark btn-sm">Arsip</a>
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary btn-sm">Data Aktif</a>
                    <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah User</a>
                </div>
            </div>

            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-5">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Nama atau email">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="anggota" @selected(request('role') === 'anggota')>Anggota</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Data Anggota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarUser as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($daftarUser->currentPage() - 1) * $daftarUser->perPage() }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>
                                    <span class="badge {{ $item->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ strtoupper($item->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->anggota)
                                        {{ $item->anggota->nama }}
                                        <div class="small text-muted">{{ $item->anggota->nis }} - {{ $item->anggota->kelas }}</div>
                                    @else
                                        <span class="text-muted">Belum terhubung</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <div class="action-wrap">
                                    @if (!empty($isArchive))
                                        <form action="{{ route('user.restore', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pulihkan user ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Pulihkan</button>
                                        </form>
                                    @else
                                        <a href="{{ route('user.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('user.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarUser->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarUser->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
