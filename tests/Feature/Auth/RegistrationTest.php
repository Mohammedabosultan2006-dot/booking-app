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
            'national_id' => '123456789',
            'phone' => '0599123456',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('services.index'));
    }

    public function test_users_can_register_without_an_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Email User',
            'national_id' => '987654321',
            'phone' => '0599654321',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('services.index'));
        $this->assertNull(auth()->user()->email);
    }

    public function test_registration_requires_a_unique_national_id(): void
    {
        \App\Models\User::factory()->create(['national_id' => '111222333']);

        $response = $this->post('/register', [
            'name' => 'Duplicate User',
            'national_id' => '111222333',
            'phone' => '0599111222',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('national_id');
        $this->assertGuest();
    }
}
