<x-app-layout>
    <x-slot name="header">
        Detail Anggota
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">Informasi Anggota</h2>
                    <table class="table table-sm mb-0">
                        <tr>
                            <th style="width: 40%;">NIS</th>
                            <td>{{ $anggota->nis }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $anggota->nama }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>{{ $anggota->kelas }}</td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td>{{ $anggota->no_hp ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $anggota->alamat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Akun User</th>
                            <td>
                                @if ($anggota->user)
                                    {{ $anggota->user->name }}
                                    <div class="small text-muted">{{ $anggota->user->email }}</div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0">Riwayat Peminjaman</h2>
                        <a href="{{ route('anggota.edit', $anggota) }}" class="btn btn-warning btn-sm">Edit Anggota</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tgl Pinjam</th>
                                    <th>Buku</th>
                                    <th>Tgl Kembali Rencana</th>
                                    <th>Tgl Kembali Aktual</th>
                                    <th>Status</th>
                                    <th>Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayat as $item)
                                    <tr>
                                        <td>{{ $item->tgl_pinjam?->format('d-m-Y') }}</td>
                                        <td>{{ $item->buku->judul ?? '-' }}</td>
                                        <td>{{ $item->tgl_kembali_rencana?->format('d-m-Y') }}</td>
                                        <td>{{ $item->tgl_kembali_aktual?->format('d-m-Y') ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $item->status === 'dipinjam' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>Rp{{ number_format($item->denda, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat peminjaman.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($riwayat->hasPages())
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $riwayat->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

