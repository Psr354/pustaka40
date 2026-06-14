<?php

namespace App\Mail;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PeminjamanDisetujuiMail extends Mailable
{
    use Queueable;

    public function __construct(
        public Peminjaman $peminjaman
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peminjaman Buku Disetujui'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.peminjaman-disetujui'
        );
    }
}
