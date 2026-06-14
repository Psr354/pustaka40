<x-guest-layout>
    <h2 class="h4 text-center mb-4">Registrasi Pengguna</h2>

    <form method="POST" action="{{ route('register') }}" data-disable-on-submit>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required minlength="3" maxlength="255" autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required maxlength="255" autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nis" class="form-label">NIS</label>
            <input id="nis" type="text" name="nis" value="{{ old('nis') }}" class="form-control @error('nis') is-invalid @enderror" required inputmode="numeric" pattern="^[0-9]{5,20}$" minlength="5" maxlength="20" placeholder="Contoh: 12345678">
            @error('nis')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input id="kelas" type="text" name="kelas" value="{{ old('kelas') }}" class="form-control @error('kelas') is-invalid @enderror" required minlength="2" maxlength="20">
            @error('kelas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="no_hp" class="form-label">No. HP</label>
            <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror" inputmode="numeric" pattern="^(\+62|62|0)[0-9]{8,15}$" placeholder="08123456789">
            @error('no_hp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" maxlength="1000">{{ old('alamat') }}</textarea>
            @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" data-submit-label="Mendaftar...">Daftar</button>
        </div>

        <p class="text-center mt-3 mb-0">
            Sudah punya akun?
            <a href="{{ route('login') }}">Login di sini</a>
        </p>
    </form>
</x-guest-layout>
