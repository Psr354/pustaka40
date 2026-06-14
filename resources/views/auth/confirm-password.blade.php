<x-guest-layout>
    <h2 class="h4 text-center mb-4">Konfirmasi Password</h2>

    <p class="text-muted mb-4">
        Demi keamanan, masukkan password Anda sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" data-disable-on-submit>
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" data-submit-label="Memproses...">Konfirmasi</button>
        </div>
    </form>
</x-guest-layout>
