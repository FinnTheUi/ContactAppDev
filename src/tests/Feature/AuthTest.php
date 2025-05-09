<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function getValidPhoneNumber(): string
    {
        return '+63' . $this->faker->numerify('#########');
    }

    public function test_user_can_view_login_page()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_view_register_page()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register.submit'), $userData);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Registration successful! You can now login.');
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
            'phone' => $userData['phone'],
        ]);
    }

    public function test_user_can_login()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_registration_validation()
    {
        $response = $this->post(route('register.submit'), [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => 'invalid-phone',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'password']);
    }

    public function test_login_validation()
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }
} 