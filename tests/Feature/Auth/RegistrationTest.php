<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nis' => '123456',
            'kelas' => 'XII IPA 1',
            'no_hp' => '08123456789',
            'alamat' => 'Jl. Perpustakaan',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'anggota',
        ]);
        $this->assertDatabaseHas('anggota', [
            'nis' => '123456',
            'nama' => 'Test User',
            'kelas' => 'XII IPA 1',
        ]);
        $response->assertRedirect(route('buku.index', absolute: false));
    }
}
