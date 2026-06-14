<x-app-layout>
    <x-slot name="header">
        Edit User
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('user.update', $user) }}" method="POST" data-disable-on-submit>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required minlength="3" maxlength="255">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required maxlength="255">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                            <option value="anggota" @selected(old('role', $user->role) === 'anggota')>Anggota</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label">Password Baru (opsional)</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8" autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" minlength="8" autocomplete="new-password">
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" data-submit-label="Mengupdate...">Update</button>
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
