<x-app-layout>
    <x-slot name="header">
        Data Peminjaman
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <h2 class="h5 mb-0">Daftar Peminjaman</h2>
                <div class="d-flex gap-2 flex-wrap">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('peminjaman.index', ['trashed' => 'only']) }}" class="btn btn-outline-dark btn-sm">Arsip</a>
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary btn-sm">Data Aktif</a>
                        <a href="{{ route('peminjaman.export', request()->query()) }}" class="btn btn-outline-success btn-sm">CSV</a>
                        <a href="{{ route('peminjaman.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm">Excel</a>
                        <a href="{{ route('peminjaman.export.pdf', request()->query()) }}" class="btn btn-outline-secondary btn-sm">PDF</a>
                        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary btn-sm">Tambah Peminjaman</a>
                    @elseif (auth()->user()->isAnggota() && auth()->user()->anggota)
                        <a href="{{ route('peminjaman.ajukan.form') }}" class="btn btn-primary btn-sm">Ajukan Peminjaman</a>
                    @endif
                </div>
            </div>

            <div class="summary-grid mb-3" aria-label="Ringkasan peminjaman">
                <div class="summary-card">
                    <div class="summary-label">Total Data</div>
                    <div class="summary-value">{{ number_format((int) ($ringkasan['total'] ?? 0)) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Menunggu ACC</div>
                    <div class="summary-value">{{ number_format((int) ($ringkasan['menunggu_acc'] ?? 0)) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Sedang Dipinjam</div>
                    <div class="summary-value">{{ number_format((int) ($ringkasan['dipinjam'] ?? 0)) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Terlambat</div>
                    <div class="summary-value text-danger">{{ number_format((int) ($ringkasan['terlambat'] ?? 0)) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Denda Belum Lunas</div>
                    <div class="summary-value">{{ number_format((int) ($ringkasan['denda_belum_lunas'] ?? 0)) }}</div>
                    <div class="small text-muted mt-1">Rp{{ number_format((float) ($ringkasan['nominal_denda_tersisa'] ?? 0), 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3" role="group" aria-label="Filter cepat status">
                <a href="{{ route('peminjaman.index', array_merge(request()->except(['status', 'page']), ['status' => 'menunggu_acc'])) }}" class="btn btn-sm toolbar-chip {{ request('status') === 'menunggu_acc' ? 'btn-warning' : 'btn-outline-warning' }}">Menunggu ACC</a>
                <a href="{{ route('peminjaman.index', array_merge(request()->except(['status', 'page']), ['status' => 'dipinjam'])) }}" class="btn btn-sm toolbar-chip {{ request('status') === 'dipinjam' ? 'btn-primary' : 'btn-outline-primary' }}">Dipinjam</a>
                <a href="{{ route('peminjaman.index', array_merge(request()->except(['status', 'page']), ['status' => 'terlambat'])) }}" class="btn btn-sm toolbar-chip {{ request('status') === 'terlambat' ? 'btn-danger' : 'btn-outline-danger' }}">Terlambat</a>
                <a href="{{ route('peminjaman.index', array_merge(request()->except(['status', 'page']), ['status' => 'dikembalikan'])) }}" class="btn btn-sm toolbar-chip {{ request('status') === 'dikembalikan' ? 'btn-success' : 'btn-outline-success' }}">Dikembalikan</a>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-sm toolbar-chip btn-outline-secondary">Lepas Filter</a>
                @if (($jumlahFilterAktif ?? 0) > 0)
                    <span class="align-self-center small text-muted">{{ $jumlahFilterAktif }} filter aktif</span>
                @endif
            </div>

            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buku / anggota / NIS">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            @if (auth()->user()->isAnggota() && ! auth()->user()->anggota)
                <div class="alert alert-warning">
                    Akun Anda belum ditautkan ke data anggota. Hubungi admin Pustaka40.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pinjam</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Rencana Kembali</th>
                            <th>Aktual Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th>Dibayar</th>
                            <th>Sisa</th>
                            @if (auth()->user()->isAdmin())
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPeminjaman as $item)
                            @php
                                $tampilanStatus = $item->status;
                                if ($item->status === 'dipinjam' && $item->tgl_kembali_rencana->isPast()) {
                                    $tampilanStatus = 'terlambat';
                                }

                                $badgeClass = match ($tampilanStatus) {
                                    'menunggu_acc' => 'bg-warning text-dark',
                                    'dipinjam' => 'bg-primary',
                                    'dikembalikan' => 'bg-success',
                                    'terlambat' => 'bg-danger',
                                    'ditolak' => 'bg-secondary',
                                    default => 'bg-light text-dark',
                                };

                                $labelStatus = match ($tampilanStatus) {
                                    'menunggu_acc' => 'Menunggu ACC',
                                    'dipinjam' => 'Dipinjam',
                                    'dikembalikan' => 'Dikembalikan',
                                    'terlambat' => 'Terlambat',
                                    'ditolak' => 'Ditolak',
                                    default => ucfirst($item->status),
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration + ($daftarPeminjaman->currentPage() - 1) * $daftarPeminjaman->perPage() }}</td>
                                <td>{{ $item->tgl_pinjam->format('d-m-Y') }}</td>
                                <td>
                                    {{ $item->anggota->nama ?? '-' }}
                                    @if ($item->anggota)
                                        <div class="small text-muted">{{ $item->anggota->nis }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->buku->judul ?? '-' }}</td>
                                <td>{{ $item->tgl_kembali_rencana->format('d-m-Y') }}</td>
                                <td>{{ $item->tgl_kembali_aktual?->format('d-m-Y') ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">{{ $labelStatus }}</span>
                                    @if ($tampilanStatus === 'terlambat')
                                        <div class="small text-danger mt-1">
                                            {{ $item->hari_terlambat }} hari lewat jatuh tempo
                                        </div>
                                    @endif
                                </td>
                                <td>Rp{{ number_format($item->total_denda, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->denda_dibayar, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->sisa_denda, 0, ',', '.') }}</td>
                                @if (auth()->user()->isAdmin())
                                    <td class="text-nowrap">
                                        <div class="action-wrap">
                                        @if (!empty($isArchive))
                                            <form action="{{ route('peminjaman.restore', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Pulihkan data peminjaman ini?')" data-disable-on-submit>
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" data-submit-label="Memulihkan...">Pulihkan</button>
                                            </form>
                                        @else
                                            @if ($item->status === 'menunggu_acc')
                                                <form action="{{ route('peminjaman.approve', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui pengajuan peminjaman ini?')" data-disable-on-submit>
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" data-submit-label="Menyetujui...">ACC</button>
                                                </form>
                                                <form action="{{ route('peminjaman.reject', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Tolak pengajuan peminjaman ini?')" data-disable-on-submit>
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" data-submit-label="Menolak...">Tolak</button>
                                                </form>
                                            @elseif ($item->status === 'dipinjam' || $tampilanStatus === 'terlambat')
                                                <form action="{{ route('peminjaman.kembalikan', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Proses pengembalian buku ini?')" data-disable-on-submit>
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary btn-sm" data-submit-label="Memproses...">Kembalikan</button>
                                                </form>
                                            @endif

                                            @if ($item->sisa_denda > 0)
                                                <form action="{{ route('peminjaman.bayar-denda', $item) }}" method="POST" class="d-inline" data-disable-on-submit>
                                                    @csrf
                                                    <input type="hidden" name="jumlah_bayar" value="{{ $item->sisa_denda }}">
                                                    <input type="hidden" name="catatan_denda" value="Pelunasan via dashboard">
                                                    <button type="submit" class="btn btn-warning btn-sm" data-submit-label="Memproses...">Lunasi Denda</button>
                                                </form>
                                            @endif

                                            @if (!in_array($item->status, ['menunggu_acc', 'dipinjam'], true))
                                                <form action="{{ route('peminjaman.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Arsipkan data peminjaman ini?')" data-disable-on-submit>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" data-submit-label="Mengarsipkan...">Arsipkan</button>
                                                </form>
                                            @endif
                                        @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 11 : 10 }}" class="text-center text-muted py-4">
                                    Belum ada data peminjaman.
                                    @if (auth()->user()->isAdmin())
                                        <div class="mt-2">
                                            <a href="{{ route('peminjaman.create') }}" class="btn btn-sm btn-primary">Tambah Peminjaman</a>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <a href="{{ route('buku.index') }}" class="btn btn-sm btn-primary">Cari Buku untuk Dipinjam</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($daftarPeminjaman->hasPages())
                <div class="mt-3 d-flex justify-content-end">
                    {{ $daftarPeminjaman->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
