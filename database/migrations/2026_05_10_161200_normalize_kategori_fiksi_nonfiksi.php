<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $fiksiId = $this->upsertKategori('Fiksi', 'Karya imajinatif seperti novel, cerpen, drama, dan puisi.');
        $nonfiksiId = $this->upsertKategori('Nonfiksi', 'Karya berbasis fakta seperti referensi, sejarah, sains, dan teknologi.');

        $kategoriRows = DB::table('kategori')
            ->select('id', 'nama_kategori')
            ->whereNull('deleted_at')
            ->get();

        $kategoriByNama = [];
        foreach ($kategoriRows as $row) {
            $kategoriByNama[mb_strtolower($row->nama_kategori)] = (int) $row->id;
        }

        $pivotRows = DB::table('buku_kategori')->select('buku_id', 'kategori_id')->get();
        $bukuKategoriMap = [];
        foreach ($pivotRows as $pivot) {
            $bukuKategoriMap[(int) $pivot->buku_id][] = (int) $pivot->kategori_id;
        }

        // Migrasi kategori lama selain Fiksi/Nonfiksi menjadi Genre.
        foreach ($kategoriRows as $row) {
            $nama = trim((string) $row->nama_kategori);
            if (in_array(mb_strtolower($nama), ['fiksi', 'nonfiksi'], true)) {
                continue;
            }

            $genreId = $this->upsertGenre($nama);

            $bukuIds = DB::table('buku_kategori')
                ->where('kategori_id', $row->id)
                ->pluck('buku_id');

            foreach ($bukuIds as $bukuId) {
                DB::table('buku_genre')->updateOrInsert(
                    ['buku_id' => $bukuId, 'genre_id' => $genreId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Setiap buku harus punya tepat 1 kategori: Fiksi atau Nonfiksi.
        $bukuIds = DB::table('buku')->whereNull('deleted_at')->pluck('id');
        DB::table('buku_kategori')->delete();

        $fiksiKategoriId = $kategoriByNama['fiksi'] ?? $fiksiId;
        $nonfiksiKategoriId = $kategoriByNama['nonfiksi'] ?? $nonfiksiId;

        foreach ($bukuIds as $bukuId) {
            $kategoriLama = $bukuKategoriMap[(int) $bukuId] ?? [];
            $targetKategori = in_array($fiksiKategoriId, $kategoriLama, true) ? $fiksiKategoriId : $nonfiksiKategoriId;

            DB::table('buku_kategori')->insert([
                'buku_id' => $bukuId,
                'kategori_id' => $targetKategori,
            ]);
        }

        // Arsipkan kategori lama yang bukan Fiksi/Nonfiksi.
        DB::table('kategori')
            ->whereNull('deleted_at')
            ->whereNotIn('id', [$fiksiId, $nonfiksiId])
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('genre')
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(nama_genre) IN (?, ?)', ['fiksi', 'nonfiksi'])
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // intentionally left blank
    }

    private function upsertKategori(string $nama, string $deskripsi): int
    {
        $existing = DB::table('kategori')
            ->whereRaw('LOWER(nama_kategori) = ?', [mb_strtolower($nama)])
            ->first();

        if ($existing) {
            DB::table('kategori')
                ->where('id', $existing->id)
                ->update([
                    'nama_kategori' => $nama,
                    'deskripsi' => $deskripsi,
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]);

            return (int) $existing->id;
        }

        return (int) DB::table('kategori')->insertGetId([
            'nama_kategori' => $nama,
            'deskripsi' => $deskripsi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function upsertGenre(string $nama): int
    {
        $existing = DB::table('genre')
            ->whereRaw('LOWER(nama_genre) = ?', [mb_strtolower($nama)])
            ->first();

        if ($existing) {
            DB::table('genre')
                ->where('id', $existing->id)
                ->update([
                    'nama_genre' => $nama,
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]);

            return (int) $existing->id;
        }

        return (int) DB::table('genre')->insertGetId([
            'nama_genre' => $nama,
            'deskripsi' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
