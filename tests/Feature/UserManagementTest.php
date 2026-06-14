<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_last_admin_can_not_be_changed_to_anggota(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('user.update', $admin), [
                'name' => 'Admin Update',
                'email' => 'admin-updated@example.com',
                'role' => 'anggota',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('error')
            ->assertRedirect(route('user.edit', $admin));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }
}
