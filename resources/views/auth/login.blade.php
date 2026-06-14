<x-guest-layout>
    <h2 class="h4 text-center mb-2">Masuk ke Pustaka40</h2>
    <p class="text-muted text-center mb-4">Login untuk meminjam buku, melihat status, atau mengelola perpustakaan.</p>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" data-disable-on-submit>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required maxlength="255" autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">Ingat saya</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" data-submit-label="Memproses...">Login</button>

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>

        @if (Route::has('register'))
            <p class="text-center mt-3 mb-0">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar di sini</a>
            </p>
        @endif
    </form>
</x-guest-layout>
