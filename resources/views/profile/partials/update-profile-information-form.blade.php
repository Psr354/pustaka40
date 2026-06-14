<section>
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h2 class="h6 mb-1">Informasi Profil</h2>
            <p class="text-muted small mb-0">Perbarui nama dan email akun Anda.</p>
        </div>
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <span class="badge text-bg-warning">Email belum terverifikasi</span>
        @endif
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" data-disable-on-submit>
        @csrf
        @method('patch')

        <div class="profile-photo-panel mb-3">
            <div class="profile-photo-preview">
                @if ($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" alt="Foto profil {{ $user->name }}">
                @else
                    <span>{{ $user->initials }}</span>
                @endif
            </div>

            <div class="profile-photo-fields">
                <label for="profile_photo" class="form-label">Foto Profil</label>
                <input id="profile_photo" name="profile_photo" type="file" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                <div class="form-text">Pilih dari galeri. Format JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                @error('profile_photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="profile-camera mt-3" data-profile-camera>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <label for="camera_photo" class="form-label mb-0">Ambil dari Kamera</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-camera-start>Buka Kamera</button>
                            <button type="button" class="btn btn-primary btn-sm" data-camera-capture disabled>Ambil Foto</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-camera-stop disabled>Tutup</button>
                        </div>
                    </div>
                    <video class="profile-camera-video d-none" data-camera-video autoplay playsinline muted></video>
                    <canvas class="d-none" data-camera-canvas></canvas>
                    <input id="camera_photo" name="camera_photo" type="file" class="form-control @error('camera_photo') is-invalid @enderror" accept="image/*" capture="environment">
                    <div class="form-text" data-camera-status>Di HP bisa membuka kamera langsung. Di desktop gunakan tombol Buka Kamera.</div>
                </div>
                @error('camera_photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if ($user->profile_photo_path)
                    <div class="form-check mt-3">
                        <input id="remove_profile_photo" name="remove_profile_photo" type="checkbox" class="form-check-input" value="1">
                        <label for="remove_profile_photo" class="form-check-label">Hapus foto profil saat ini</label>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required minlength="3" maxlength="255" autofocus autocomplete="name" />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="username" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-3 mb-0">
                    <div class="d-flex flex-column gap-1">
                        <span>Email Anda belum terverifikasi.</span>
                        <button form="send-verification" class="btn btn-link p-0 align-self-start">Kirim ulang email verifikasi</button>
                        @if (session('status') === 'verification-link-sent')
                            <span class="text-success small">Tautan verifikasi baru telah dikirim.</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary" data-submit-label="Menyimpan...">Simpan</button>
            @if (session('status') === 'profile-updated')
                <span class="text-success small">Tersimpan.</span>
            @endif
        </div>
    </form>
</section>
