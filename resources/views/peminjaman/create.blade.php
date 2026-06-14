<x-app-layout>
    <x-slot name="header">
        Tambah Peminjaman
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('peminjaman.store') }}" method="POST" data-disable-on-submit>
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="anggota_id" class="form-label">Anggota</label>
                        <select id="anggota_id" name="anggota_id" class="form-select @error('anggota_id') is-invalid @enderror" required>
                            <option value="">-- Pilih anggota --</option>
                            @foreach ($daftarAnggota as $anggota)
                                <option value="{{ $anggota->id }}" @selected(old('anggota_id') == $anggota->id)>
                                    {{ $anggota->nama }} ({{ $anggota->nis }}) - {{ $anggota->kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('anggota_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="buku_id" class="form-label">Buku</label>
                        <select id="buku_id" name="buku_id" class="form-select @error('buku_id') is-invalid @enderror" required>
                            <option value="">-- Pilih buku --</option>
                            @foreach ($daftarBuku as $buku)
                                <option value="{{ $buku->id }}" @selected(old('buku_id') == $buku->id)>
                                    {{ $buku->judul }} - {{ $buku->pengarang }} (Stok: {{ $buku->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('buku_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tgl_pinjam" class="form-label">Tanggal Pinjam</label>
                        <input type="date" id="tgl_pinjam" name="tgl_pinjam" value="{{ old('tgl_pinjam', now()->toDateString()) }}" min="{{ now()->toDateString() }}" class="form-control @error('tgl_pinjam') is-invalid @enderror" required>
                        <div class="form-text">Tanggal kembali akan diisi otomatis +7 hari, masih bisa diubah manual.</div>
                        @error('tgl_pinjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tgl_kembali_rencana" class="form-label">Tanggal Kembali (Rencana)</label>
                        <input type="date" id="tgl_kembali_rencana" name="tgl_kembali_rencana" value="{{ old('tgl_kembali_rencana') }}" class="form-control @error('tgl_kembali_rencana') is-invalid @enderror" required>
                        @error('tgl_kembali_rencana')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" data-submit-label="Menyimpan...">Simpan</button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
