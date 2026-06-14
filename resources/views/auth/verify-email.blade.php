<x-guest-layout>
    <h2 class="h4 text-center mb-4">Verifikasi Email</h2>

    <p class="text-muted mb-4">
        Sebelum mulai, verifikasi email Anda melalui tautan yang sudah dikirim. Jika belum menerima email, kirim ulang lewat tombol di bawah.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            Link verifikasi baru telah dikirim ke email Anda.
        </div>
    @endif

    <div class="d-grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">Kirim Ulang Email Verifikasi</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
        </form>
    </div>
</x-guest-layout>
