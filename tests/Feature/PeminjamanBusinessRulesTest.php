<?php

namespace Tests\Feature;

use App\Mail\PeminjamanDisetujuiMail;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PeminjamanBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_tidak_bisa_meminjam_buku_yang_sama_dua_kali_sebelum_dikembalikan(): void
    {
        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = Buku::query()->create([
            'judul' => 'Buku A',
            'pengarang' => 'Pengarang A',
            'tahun_terbit' => 2020,
            'stok' => 3,
        ]);

        $payload = [
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_rencana' => now()->addDays(7)->toDateString(),
        ];

        $this->actingAs($admin)->post(route('peminjaman.store'), $payload);
        $this->actingAs($admin)->from(route('peminjaman.create'))->post(route('peminjaman.store'), $payload);

        $this->assertDatabaseCount('peminjaman', 1);
    }

    public function test_stok_berkurang_saat_pinjam_dan_bertambah_saat_kembali_dengan_denda_terhitung(): void
    {
        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = Buku::query()->create([
            'judul' => 'Buku B',
            'pengarang' => 'Pengarang B',
            'tahun_terbit' => 2021,
            'stok' => 1,
        ]);

        $this->actingAs($admin)->post(route('peminjaman.store'), [
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->subDays(10)->toDateString(),
            'tgl_kembali_rencana' => now()->subDays(3)->toDateString(),
        ]);

        $buku->refresh();
        $this->assertSame(0, $buku->stok);

        $pinjaman = Peminjaman::query()->firstOrFail();
        $this->actingAs($admin)->post(route('peminjaman.kembalikan', $pinjaman));

        $pinjaman->refresh();
        $buku->refresh();

        $this->assertSame('dikembalikan', $pinjaman->status);
        $this->assertSame(3000, $pinjaman->denda);
        $this->assertSame(1, $buku->stok);
    }

    public function test_denda_terlambat_aktif_ditampilkan_sebelum_buku_dikembalikan(): void
    {
        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku Terlambat Aktif');

        $pinjaman = Peminjaman::query()->create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->subDays(12)->toDateString(),
            'tgl_kembali_rencana' => now()->subDays(4)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('peminjaman.index', ['status' => 'terlambat']));

        $response->assertOk();
        $response->assertSee('4 hari lewat jatuh tempo');
        $response->assertSee('Rp4.000');
        $this->assertSame(4000, $pinjaman->refresh()->denda);
    }

    public function test_limit_pinjaman_aktif_per_anggota_diterapkan(): void
    {
        config()->set('perpus.max_pinjaman_aktif', 2);

        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();

        $bukuA = $this->createBuku('Buku 1');
        $bukuB = $this->createBuku('Buku 2');
        $bukuC = $this->createBuku('Buku 3');

        foreach ([$bukuA, $bukuB] as $buku) {
            $this->actingAs($admin)->post(route('peminjaman.store'), [
                'anggota_id' => $anggota->id,
                'buku_id' => $buku->id,
                'tgl_pinjam' => now()->toDateString(),
                'tgl_kembali_rencana' => now()->addDays(5)->toDateString(),
            ]);
        }

        $this->actingAs($admin)
            ->from(route('peminjaman.create'))
            ->post(route('peminjaman.store'), [
                'anggota_id' => $anggota->id,
                'buku_id' => $bukuC->id,
                'tgl_pinjam' => now()->toDateString(),
                'tgl_kembali_rencana' => now()->addDays(5)->toDateString(),
            ]);

        $this->assertDatabaseCount('peminjaman', 2);
    }

    public function test_anggota_tidak_bisa_dihapus_jika_masih_punya_pinjaman_aktif(): void
    {
        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku Aktif');

        Peminjaman::query()->create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_rencana' => now()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0,
        ]);

        $this->actingAs($admin)->delete(route('anggota.destroy', $anggota));

        $this->assertDatabaseHas('anggota', [
            'id' => $anggota->id,
            'deleted_at' => null,
        ]);
    }

    public function test_email_notifikasi_dikirim_saat_peminjaman_berhasil_dibuat(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku Email');

        $this->actingAs($admin)->post(route('peminjaman.store'), [
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_rencana' => now()->addDays(7)->toDateString(),
        ]);

        Mail::assertSent(PeminjamanDisetujuiMail::class, function (PeminjamanDisetujuiMail $mail) use ($anggota) {
            return $mail->hasTo($anggota->user?->email);
        });
    }

    public function test_export_pdf_menghasilkan_file_pdf(): void
    {
        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku PDF');

        Peminjaman::query()->create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_rencana' => now()->addDays(3)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('peminjaman.export.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_anggota_bisa_ajukan_dari_halaman_buku_dan_admin_bisa_menyetujui(): void
    {
        Mail::fake();

        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku Pengajuan');

        $this->actingAs($anggota->user)->post(route('peminjaman.ajukan'), [
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_rencana' => now()->addDays(7)->toDateString(),
        ]);

        $pengajuan = Peminjaman::query()->firstOrFail();
        $this->assertSame('menunggu_acc', $pengajuan->status);
        $this->assertSame(1, $buku->refresh()->stok);

        $this->actingAs($admin)->post(route('peminjaman.approve', $pengajuan));

        $pengajuan->refresh();
        $this->assertSame('dipinjam', $pengajuan->status);
        $this->assertSame(0, $buku->refresh()->stok);

        Mail::assertSent(PeminjamanDisetujuiMail::class, function (PeminjamanDisetujuiMail $mail) use ($anggota) {
            return $mail->hasTo($anggota->user?->email);
        });
    }

    public function test_admin_bisa_menolak_pengajuan_dan_stok_tidak_berubah(): void
    {
        $admin = $this->createAdmin();
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku Tolak');

        $this->actingAs($anggota->user)->post(route('peminjaman.ajukan'), [
            'buku_id' => $buku->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_rencana' => now()->addDays(7)->toDateString(),
        ]);
        $pengajuan = Peminjaman::query()->firstOrFail();

        $this->actingAs($admin)->post(route('peminjaman.reject', $pengajuan));

        $pengajuan->refresh();
        $this->assertSame('ditolak', $pengajuan->status);
        $this->assertSame(1, $buku->refresh()->stok);
    }

    public function test_pengajuan_anggota_wajib_tanggal_valid(): void
    {
        $anggota = $this->createAnggota();
        $buku = $this->createBuku('Buku Validasi Tanggal');

        $response = $this->actingAs($anggota->user)
            ->from(route('peminjaman.ajukan.form'))
            ->post(route('peminjaman.ajukan'), [
                'buku_id' => $buku->id,
                'tgl_pinjam' => now()->subDay()->toDateString(),
                'tgl_kembali_rencana' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('peminjaman.ajukan.form'));
        $response->assertSessionHasErrors(['tgl_pinjam']);

        $response = $this->actingAs($anggota->user)
            ->from(route('peminjaman.ajukan.form'))
            ->post(route('peminjaman.ajukan'), [
                'buku_id' => $buku->id,
                'tgl_pinjam' => now()->toDateString(),
                'tgl_kembali_rencana' => now()->toDateString(),
            ]);

        $response->assertRedirect(route('peminjaman.ajukan.form'));
        $response->assertSessionHasErrors(['tgl_kembali_rencana']);
        $this->assertDatabaseCount('peminjaman', 0);
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function createAnggota(): Anggota
    {
        $user = User::factory()->create([
            'role' => 'anggota',
        ]);

        return Anggota::query()->create([
            'user_id' => $user->id,
            'nis' => (string) fake()->unique()->numerify('2024###'),
            'nama' => fake()->name(),
            'kelas' => 'X-RPL-1',
            'no_hp' => '081234567890',
            'alamat' => 'Alamat Test',
        ]);
    }

    private function createBuku(string $judul): Buku
    {
        return Buku::query()->create([
            'judul' => $judul,
            'pengarang' => 'Pengarang',
            'tahun_terbit' => 2020,
            'stok' => 1,
        ]);
    }
}
