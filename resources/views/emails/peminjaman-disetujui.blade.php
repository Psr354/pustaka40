<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Peminjaman Buku Disetujui</title>
</head>
<body>
    <p>Halo {{ $peminjaman->anggota?->nama ?? 'Anggota' }},</p>

    <p>Peminjaman buku Anda sudah disetujui dengan detail berikut:</p>

    <ul>
        <li>ID Peminjaman: {{ $peminjaman->id }}</li>
        <li>Judul Buku: {{ $peminjaman->buku?->judul ?? '-' }}</li>
        <li>Pengarang: {{ $peminjaman->buku?->pengarang ?? '-' }}</li>
        <li>Tanggal Pinjam: {{ $peminjaman->tgl_pinjam?->format('d-m-Y') }}</li>
        <li>Tanggal Kembali (Rencana): {{ $peminjaman->tgl_kembali_rencana?->format('d-m-Y') }}</li>
    </ul>

    <p>Harap kembalikan buku tepat waktu untuk menghindari denda.</p>
    <p>Terima kasih.</p>
</body>
</html>
