<x-app-layout>
    <x-slot name="header">
        Edit Buku
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('buku.update', $buku) }}" method="POST" enctype="multipart/form-data" data-disable-on-submit>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="cover" class="form-label">Cover Buku</label>
                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
                        <div class="book-cover-preview">
                            @if ($buku->cover_url)
                                <img src="{{ $buku->cover_url }}" alt="Cover {{ $buku->judul }}">
                            @else
                                <span>Belum ada cover</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" id="cover" name="cover" class="form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Kosongkan jika tidak ingin mengganti cover. Format JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                            @error('cover')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul', $buku->judul) }}" class="form-control @error('judul') is-invalid @enderror" required minlength="2" maxlength="255">
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pengarang" class="form-label">Pengarang</label>
                    <input type="text" id="pengarang" name="pengarang" value="{{ old('pengarang', $buku->pengarang) }}" class="form-control @error('pengarang') is-invalid @enderror" required minlength="2" maxlength="255">
                    @error('pengarang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" maxlength="2000">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                    <div class="form-text">Opsional, tetapi sebaiknya diisi agar anggota bisa menilai isi buku.</div>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" class="form-control @error('tahun_terbit') is-invalid @enderror" min="1900" max="{{ date('Y') }}" required>
                        @error('tahun_terbit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" id="stok" name="stok" value="{{ old('stok', $buku->stok) }}" class="form-control @error('stok') is-invalid @enderror" min="0" max="100000" required>
                        @error('stok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Kategori</label>
                    @php($selectedKategori = old('kategori', $buku->kategori->pluck('id')->all()))
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($daftarKategori as $kategori)
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="kategori[]"
                                    value="{{ $kategori->id }}"
                                    id="kategori-edit-{{ $kategori->id }}"
                                    data-kategori-single
                                    @checked(in_array($kategori->id, $selectedKategori, true))
                                >
                                <label class="form-check-label" for="kategori-edit-{{ $kategori->id }}">
                                    {{ $kategori->nama_kategori }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">Pilih 1 kategori utama (Fiksi atau Nonfiksi).</div>
                    @error('kategori')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <label class="form-label">Genre</label>
                    @php($selectedGenre = old('genre', $buku->genre->pluck('id')->all()))
                    @include('buku.partials.kategori-multiselect', [
                        'daftarOpsi' => $daftarGenre,
                        'selectedOpsi' => $selectedGenre,
                        'inputId' => 'genre-edit',
                        'fieldName' => 'genre',
                        'optionLabelKey' => 'nama_genre',
                        'searchPlaceholder' => 'Cari genre...',
                        'labelSingular' => 'genre',
                        'errorKey' => 'genre',
                    ])
                    @error('genre')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" data-submit-label="Mengupdate...">Update</button>
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
