<x-app-layout>
    <x-slot name="header">
        Notifikasi Peminjaman
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Pesan</th>
                            <th>Status Baca</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarNotifikasi as $item)
                            <tr>
                                <td>{{ $item->tanggal_notifikasi?->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge {{ $item->jenis === 'terlambat' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ str_replace('_', ' ', strtoupper($item->jenis)) }}
                                    </span>
                                </td>
                                <td>{{ $item->anggota->nama ?? '-' }}</td>
                                <td>{{ $item->peminjaman?->buku?->judul ?? '-' }}</td>
                                <td>{{ $item->pesan }}</td>
                                <td>
                                    @if ($item->dibaca_pada)
                                        <span class="badge bg-success">Sudah Dibaca</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Dibaca</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada notifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarNotifikasi->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarNotifikasi->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
