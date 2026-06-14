<?php

namespace App\Services;

use App\Mail\PeminjamanDisetujuiMail;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Mail;

class PeminjamanMailerService
{
    public function kirimPeminjamanDisetujui(Peminjaman $peminjaman): void
    {
        $pinjaman = Peminjaman::query()
            ->with(['anggota.user:id,email,name', 'buku:id,judul,pengarang'])
            ->find($peminjaman->id);

        $email = $pinjaman?->anggota?->user?->email;

        if (! $email || ! $pinjaman) {
            return;
        }

        try {
            Mail::to($email)->send(new PeminjamanDisetujuiMail($pinjaman));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
