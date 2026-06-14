<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@perpus.test',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $anggotaUsers = collect([
            [
                'name' => 'Anggota Satu',
                'email' => 'anggota1@perpus.test',
                'nis' => '2024001',
                'kelas' => 'X-A',
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Melati No. 1',
            ],
            [
                'name' => 'Anggota Dua',
                'email' => 'anggota2@perpus.test',
                'nis' => '2024002',
                'kelas' => 'X-B',
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Kenanga No. 2',
            ],
            [
                'name' => 'Anggota Tiga',
                'email' => 'anggota3@perpus.test',
                'nis' => '2024003',
                'kelas' => 'XI-A',
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Mawar No. 3',
            ],
            [
                'name' => 'Anggota Empat',
                'email' => 'anggota4@perpus.test',
                'nis' => '2024004',
                'kelas' => 'XI-B',
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Anggrek No. 4',
            ],
            [
                'name' => 'Anggota Lima',
                'email' => 'anggota5@perpus.test',
                'nis' => '2024005',
                'kelas' => 'XII-A',
                'no_hp' => '081234567895',
                'alamat' => 'Jl. Dahlia No. 5',
            ],
        ])->map(function (array $data) {
            $user = User::factory()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'anggota',
                'password' => Hash::make('password'),
            ]);

            $anggota = new Anggota([
                'nis' => $data['nis'],
                'nama' => $data['name'],
                'kelas' => $data['kelas'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
            ]);
            $anggota->id = $user->id;
            $anggota->save();

            return $anggota;
        });

        $kategoriTeknologi = Kategori::query()->create([
            'nama_kategori' => 'Teknologi',
            'deskripsi' => 'Buku seputar teknologi dan pemrograman.',
        ]);
        $kategoriFiksi = Kategori::query()->create([
            'nama_kategori' => 'Fiksi',
            'deskripsi' => 'Koleksi cerita dan novel fiksi.',
        ]);
        $kategoriSejarah = Kategori::query()->create([
            'nama_kategori' => 'Sejarah',
            'deskripsi' => 'Kumpulan buku sejarah dan biografi.',
        ]);

        $bukuData = [
            [
                'judul' => 'Laravel in Action',
                'pengarang' => 'Nina K.',
                'tahun_terbit' => 2021,
                'stok' => 4,
                'kategori' => [$kategoriTeknologi->id],
            ],
            [
                'judul' => 'Algoritma Dasar',
                'pengarang' => 'R. Putra',
                'tahun_terbit' => 2017,
                'stok' => 5,
                'kategori' => [$kategoriTeknologi->id],
            ],
            [
                'judul' => 'Sejarah Nusantara',
                'pengarang' => 'Dewi L.',
                'tahun_terbit' => 2015,
                'stok' => 3,
                'kategori' => [$kategoriSejarah->id],
            ],
            [
                'judul' => 'Cerita Fiksi Modern',
                'pengarang' => 'A. Mahesa',
                'tahun_terbit' => 2020,
                'stok' => 2,
                'kategori' => [$kategoriFiksi->id],
            ],
            [
                'judul' => 'Pemrograman Web Praktis',
                'pengarang' => 'S. Wijaya',
                'tahun_terbit' => 2019,
                'stok' => 4,
                'kategori' => [$kategoriTeknologi->id],
            ],
            [
                'judul' => 'Legenda Kota Tua',
                'pengarang' => 'Mira S.',
                'tahun_terbit' => 2016,
                'stok' => 3,
                'kategori' => [$kategoriFiksi->id, $kategoriSejarah->id],
            ],
            [
                'judul' => 'Database untuk Pemula',
                'pengarang' => 'Y. Firmansyah',
                'tahun_terbit' => 2022,
                'stok' => 6,
                'kategori' => [$kategoriTeknologi->id],
            ],
            [
                'judul' => 'Kisah Pahlawan',
                'pengarang' => 'Laras H.',
                'tahun_terbit' => 2014,
                'stok' => 2,
                'kategori' => [$kategoriFiksi->id],
            ],
        ];

        $bukuList = collect($bukuData)->map(function (array $data) {
            $kategoriIds = $data['kategori'];
            unset($data['kategori']);

            $buku = Buku::query()->create($data);
            $buku->kategori()->sync($kategoriIds);

            return $buku;
        });

        $now = Carbon::now();
        $anggotaPertama = $anggotaUsers->first();
        $anggotaKedua = $anggotaUsers->skip(1)->first();
        $anggotaKetiga = $anggotaUsers->skip(2)->first();

        $peminjamanAktif = Peminjaman::query()->create([
            'anggota_id' => $anggotaPertama->id,
            'buku_id' => $bukuList->first()->id,
            'tgl_pinjam' => $now->copy()->toDateString(),
            'tgl_kembali_rencana' => $now->copy()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0,
        ]);

        $bukuList->first()->decrement('stok');

        Peminjaman::query()->create([
            'anggota_id' => $anggotaKedua->id,
            'buku_id' => $bukuList->get(3)->id,
            'tgl_pinjam' => $now->copy()->subDays(12)->toDateString(),
            'tgl_kembali_rencana' => $now->copy()->subDays(5)->toDateString(),
            'tgl_kembali_aktual' => $now->copy()->subDays(2)->toDateString(),
            'status' => 'terlambat',
            'denda' => 3000,
        ]);

        Peminjaman::query()->create([
            'anggota_id' => $anggotaKetiga->id,
            'buku_id' => $bukuList->get(2)->id,
            'tgl_pinjam' => $now->copy()->subDays(6)->toDateString(),
            'tgl_kembali_rencana' => $now->copy()->subDays(1)->toDateString(),
            'tgl_kembali_aktual' => $now->copy()->subDays(1)->toDateString(),
            'status' => 'dikembalikan',
            'denda' => 0,
        ]);
    }
}
