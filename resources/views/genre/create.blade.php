<x-app-layout>
    <x-slot name="header">
        Tambah Genre
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('genre.store') }}" method="POST" data-disable-on-submit>
                @csrf

                <div class="mb-3">
                    <label for="nama_genre" class="form-label">Nama Genre</label>
                    <input type="text" id="nama_genre" name="nama_genre" value="{{ old('nama_genre') }}" class="form-control @error('nama_genre') is-invalid @enderror" required minlength="2" maxlength="100">
                    @error('nama_genre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" maxlength="1000">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary" data-submit-label="Menyimpan...">Simpan</button>
                    <a href="{{ route('genre.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
