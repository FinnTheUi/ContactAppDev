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

    public function test_registration_phone_number_validation()
    {
        $invalidPhones = [
            '123', // Too short
            '1234567890123456', // Too long
            'abc123456789', // Contains letters
            '+123456789', // Invalid country code
        ];

        foreach ($invalidPhones as $phone) {
            $response = $this->post(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $phone,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertSessionHasErrors('phone');
        }
    }

    public function test_registration_password_validation()
    {
        $invalidPasswords = [
            'short', // Too short
            'onlylowercase', // No uppercase
            'ONLYUPPERCASE', // No lowercase
            'NoNumbers', // No numbers
            '12345678', // No letters
        ];

        foreach ($invalidPasswords as $password) {
            $response = $this->post(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $this->getValidPhoneNumber(),
                'password' => $password,
                'password_confirmation' => $password,
            ]);

            $response->assertSessionHasErrors('password');
        }
    }

    public function test_registration_email_validation()
    {
        $invalidEmails = [
            'plainaddress', // No @ symbol
            '@domain.com', // No local part
            'user@', // No domain
            'user@domain', // No TLD
            'user@.com', // No domain name
            'user@domain..com', // Double dot
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->post(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $email,
                'phone' => $this->getValidPhoneNumber(),
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertSessionHasErrors('email');
        }
    }

    public function test_registration_name_validation()
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'a', // Too short
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_login_validation()
    {
        $response = $this->post(route('login.submit'), [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_email_validation()
    {
        $invalidEmails = [
            'plainaddress',
            '@domain.com',
            'user@',
            'user@domain',
            'user@.com',
            'user@domain..com',
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->post(route('login.submit'), [
                'email' => $email,
                'password' => 'password123',
            ]);

            $response->assertSessionHasErrors('email');
        }
    }

    public function test_login_password_required()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_password_confirmation_validation()
    {
        $response = $this->post(route('register.submit'), [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123',
            'password_confirmation' => 'DifferentPassword123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_email_uniqueness()
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->post(route('register.submit'), [
            'name' => $this->faker->name,
            'email' => $existingUser->email,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_phone_uniqueness()
    {
        $existingUser = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->post(route('register.submit'), [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $existingUser->phone,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_registration_name_length_validation()
    {
        $response = $this->post(route('register.submit'), [
            'name' => str_repeat('a', 256), // Too long
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_login_attempts_throttling()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'phone' => $this->getValidPhoneNumber(),
        ]);

        // Attempt to login 6 times with wrong password
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be throttled
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_with_special_characters()
    {
        $userData = [
            'name' => 'John Doe!@#$%^&*()',
            'email' => 'test.special+chars@example.com',
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
        ];

        $response = $this->post(route('register.submit'), $userData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);
    }

    public function test_registration_with_international_phone()
    {
        $internationalPhones = [
            '+1-234-567-8901', // US format
            '+44 20 7123 4567', // UK format
            '+81-3-1234-5678', // Japan format
        ];

        foreach ($internationalPhones as $phone) {
            $userData = [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $phone,
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ];

            $response = $this->post(route('register.submit'), $userData);
            $response->assertSessionHasErrors('phone');
        }
    }
} 