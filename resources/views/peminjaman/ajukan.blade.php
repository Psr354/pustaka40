<x-app-layout>
    <x-slot name="header">
        Ajukan Peminjaman
    </x-slot>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="alert alert-info py-2 mb-3">
                Isi tanggal pinjam dan rencana kembali terlebih dahulu. Pengajuan akan berstatus <strong>menunggu ACC</strong> admin.
            </div>

            <form action="{{ route('peminjaman.ajukan') }}" method="POST" data-disable-on-submit>
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label for="buku_id" class="form-label">Buku</label>
                        <select id="buku_id" name="buku_id" class="form-select @error('buku_id') is-invalid @enderror" required>
                            <option value="">-- Pilih buku --</option>
                            @foreach ($daftarBuku as $buku)
                                <option value="{{ $buku->id }}" @selected(old('buku_id', $preselectedBukuId) == $buku->id)>
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
                        <input type="date" id="tgl_pinjam" name="tgl_pinjam"
                               value="{{ old('tgl_pinjam', $tanggalPinjamDefault) }}"
                               min="{{ now()->toDateString() }}"
                               class="form-control @error('tgl_pinjam') is-invalid @enderror"
                               required>
                        <div class="form-text">Tanggal pinjam tidak boleh sebelum hari ini.</div>
                        @error('tgl_pinjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tgl_kembali_rencana" class="form-label">Tanggal Kembali (Rencana)</label>
                        <input type="date" id="tgl_kembali_rencana" name="tgl_kembali_rencana"
                               value="{{ old('tgl_kembali_rencana', $tanggalKembaliDefault) }}"
                               class="form-control @error('tgl_kembali_rencana') is-invalid @enderror"
                               required>
                        <div class="form-text">Default {{ $durasiDefault }} hari dari tanggal pinjam. Bisa diubah sesuai kebutuhan.</div>
                        @error('tgl_kembali_rencana')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" data-submit-label="Mengirim pengajuan...">Kirim Pengajuan</button>
                    <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Kembali ke Buku</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
