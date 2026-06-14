<section>
    <div class="mb-3">
        <h2 class="h6 mb-1">Ubah Password</h2>
        <p class="text-muted small mb-0">Gunakan kata sandi yang kuat dan sulit ditebak.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" data-disable-on-submit>
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" required autocomplete="current-password" />
            @if ($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Password Baru</label>
            <input id="update_password_password" name="password" type="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" required minlength="8" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" required minlength="8" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary" data-submit-label="Menyimpan...">Simpan</button>
            @if (session('status') === 'password-updated')
                <span class="text-success small">Tersimpan.</span>
            @endif
        </div>
    </form>
</section>
