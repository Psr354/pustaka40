<x-app-layout>
    <x-slot name="header">
        Audit Log
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Cari Aksi/Entitas</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="contoh: create / buku / peminjaman">
                </div>
                <div class="col-12 col-md-6">
                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                    <a href="{{ route('audit-log.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Entitas</th>
                            <th>ID Entitas</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarLog as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                                <td>{{ $log->user?->name ?? 'System' }}</td>
                                <td>{{ $log->aksi }}</td>
                                <td>{{ $log->entitas }}</td>
                                <td>{{ $log->entitas_id }}</td>
                                <td>{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada log aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarLog->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarLog->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

