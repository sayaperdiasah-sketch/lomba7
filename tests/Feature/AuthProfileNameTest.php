<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthProfileNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_persists_name(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Nama Register',
            'email' => 'register@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'register@example.com',
            'name' => 'Nama Register',
        ]);
    }

    public function test_profile_update_persists_name(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'nama' => 'Nama Baru',
            'usia' => 25,
            'gender' => 'pria',
        ]);

        $response->assertOk()->assertJsonPath('profile.nama', 'Nama Baru');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'nama' => 'Nama Baru',
        ]);

        $this->get(route('tes-mental'))
            ->assertOk()
            ->assertSee('window.serverProfileComplete = true', false);
    }

    public function test_profile_update_stores_avatar(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'nama' => 'Pengguna Foto',
            'usia' => 25,
            'gender' => 'pria',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk()->assertJsonPath('profile.nama', 'Pengguna Foto');
        $profile = $user->fresh()->profile;

        $this->assertNotNull($profile->avatar);
        $this->assertTrue(Storage::disk('public')->exists($profile->avatar));
    }
}