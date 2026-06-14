<x-app-layout>
    <x-slot name="header">
        Edit Kategori
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('kategori.update', $kategori) }}" method="POST" data-disable-on-submit>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <select id="nama_kategori" name="nama_kategori" class="form-select @error('nama_kategori') is-invalid @enderror" required>
                        <option value="">Pilih kategori</option>
                        <option value="Fiksi" @selected(old('nama_kategori', $kategori->nama_kategori) === 'Fiksi')>Fiksi</option>
                        <option value="Nonfiksi" @selected(old('nama_kategori', $kategori->nama_kategori) === 'Nonfiksi')>Nonfiksi</option>
                    </select>
                    @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" maxlength="1000">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary" data-submit-label="Mengupdate...">Update</button>
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
