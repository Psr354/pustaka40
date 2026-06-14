<x-app-layout>
    <x-slot name="header">
        Tambah Anggota
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('anggota.store') }}" method="POST" data-disable-on-submit>
                @csrf

                <div class="mb-3">
                    <label for="user_id" class="form-label">Tautkan Akun User (Opsional)</label>
                    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                        <option value="">-- Tidak ditautkan --</option>
                        @foreach ($daftarUser as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="nis" class="form-label">NIS</label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis') }}" class="form-control @error('nis') is-invalid @enderror" required inputmode="numeric" pattern="^[0-9]{5,20}$" minlength="5" maxlength="20" placeholder="Contoh: 12345678">
                        @error('nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required minlength="3" maxlength="100">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" id="kelas" name="kelas" value="{{ old('kelas') }}" class="form-control @error('kelas') is-invalid @enderror" required minlength="2" maxlength="20">
                        @error('kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="tel" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror" inputmode="numeric" pattern="^(\+62|62|0)[0-9]{8,15}$" placeholder="08123456789">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" maxlength="1000">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" data-submit-label="Menyimpan...">Simpan</button>
                    <a href="{{ route('anggota.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
