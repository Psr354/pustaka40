<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h1 { margin: 0 0 4px; font-size: 20px; }
        p.meta { margin: 0 0 16px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; vertical-align: top; }
        th { background: #eee; text-align: left; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman</h1>
    <p class="meta">Tanggal cetak: {{ now()->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tgl Pinjam</th>
                <th>Anggota</th>
                <th>Buku</th>
                <th>Rencana</th>
                <th>Aktual</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Dibayar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $item)
                @php
                    $status = $item->status;
                    if ($item->status === 'dipinjam' && $item->tgl_kembali_rencana->isPast()) {
                        $status = 'terlambat';
                    }
                @endphp
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->tgl_pinjam->format('d-m-Y') }}</td>
                    <td>{{ $item->anggota?->nama }} ({{ $item->anggota?->nis }})</td>
                    <td>{{ $item->buku?->judul }}</td>
                    <td>{{ $item->tgl_kembali_rencana->format('d-m-Y') }}</td>
                    <td>{{ $item->tgl_kembali_aktual?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ ucfirst($status) }}</td>
                    <td>Rp{{ number_format($item->total_denda, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->denda_dibayar, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->sisa_denda, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
