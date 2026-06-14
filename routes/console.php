<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {email?}', function (?string $email = null) {
    $recipient = $email ?: config('mail.from.address');

    $this->line('Mailer: ' . config('mail.default'));
    $this->line('Host: ' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port'));
    $this->line('Scheme: ' . (config('mail.mailers.smtp.scheme') ?: '-'));
    $this->line('Username: ' . (config('mail.mailers.smtp.username') ?: '-'));
    $this->line('From: ' . config('mail.from.address'));
    $this->line('To: ' . $recipient);

    try {
        Mail::raw('Email test Pustaka40 berhasil dikirim. Jika email ini masuk, forgot password juga bisa mengirim link reset.', function ($message) use ($recipient) {
            $message->to($recipient)->subject('Test Email Pustaka40');
        });
    } catch (Throwable $exception) {
        $this->error('Gagal mengirim email: ' . $exception->getMessage());

        return self::FAILURE;
    }

    $this->info('Email test berhasil dikirim. Cek inbox atau folder spam.');

    return self::SUCCESS;
})->purpose('Test konfigurasi email SMTP aplikasi');
