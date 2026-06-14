<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuditLogFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_tercatat_saat_tambah_buku(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $kategori = Kategori::query()->firstWhere('nama_kategori', 'Nonfiksi');

        if (! $kategori) {
            $kategori = Kategori::query()->create([
                'nama_kategori' => 'Nonfiksi',
                'deskripsi' => 'Kategori test',
            ]);
        }

        $this->actingAs($admin)->post(route('buku.store'), [
            'judul' => 'Audit Buku',
            'pengarang' => 'Pengarang Audit',
            'tahun_terbit' => 2022,
            'stok' => 5,
            'cover' => UploadedFile::fake()->image('audit-buku.jpg', 600, 800),
            'kategori' => [$kategori->id],
        ]);

        $buku = Buku::query()->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'aksi' => 'create',
            'entitas' => 'buku',
            'entitas_id' => $buku->id,
        ]);
    }
}
