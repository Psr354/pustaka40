<x-guest-layout>
    <h2 class="h4 text-center mb-2">Pulihkan akses akun</h2>

    <p class="text-muted text-center mb-4">
        Masukkan email yang terdaftar. Kami akan mengirim link aman untuk membuat password baru.
    </p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" data-disable-on-submit>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required maxlength="255" autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" data-submit-label="Mengirim...">Kirim Link Reset Password</button>
            <a href="{{ route('login') }}" class="btn btn-link">Kembali ke login</a>
        </div>
    </form>
</x-guest-layout>
