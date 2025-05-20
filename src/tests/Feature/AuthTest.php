<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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
        $response->assertStatus(200, 'Expected HTTP 200 OK status');
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_view_register_page()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200, 'Expected HTTP 200 OK status');
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson(route('register.submit'), $userData);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Registration successful! You can now login.']);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
            'phone' => $userData['phone'],
        ]);
    }

    public function test_user_can_login()
    {
        /** @var User */
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->postJson(route('login.submit'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Login successful'])
                ->assertJsonStructure(['message', 'user']);

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        /** @var User */
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->postJson(route('login.submit'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Invalid credentials']);

        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        /** @var User */
        $user = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);
        $this->actingAs($user);

        $response = $this->postJson(route('logout'));

        $response->assertStatus(200)
                ->assertJson(['message' => 'Logged out successfully']);

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard()
    {
        /** @var User */
        $user = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);
        $this->actingAs($user);

        $response = $this->getJson(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->getJson(route('dashboard'));

        $response->assertStatus(401)
                ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_registration_validation()
    {
        $response = $this->postJson(route('register.submit'), [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => 'invalid-phone',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'phone', 'password']);
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
            $response = $this->postJson(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $phone,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['phone']);
        }
    }

    public function test_registration_password_validation()
    {
        $invalidPasswords = [
            'short', // Too short
            'onlylowercase', // No uppercase
            'ONLYUPPERCASE', // No lowercase
            'NoSpecial', // No numbers
            '12345678', // No letters
            'password', // No uppercase or numbers
            'PASSWORD', // No lowercase or numbers
            '123456789', // No letters
        ];

        foreach ($invalidPasswords as $password) {
            $response = $this->postJson(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $this->getValidPhoneNumber(),
                'password' => $password,
                'password_confirmation' => $password,
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['password']);
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
            $response = $this->postJson(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $email,
                'phone' => $this->getValidPhoneNumber(),
                'password' => 'Password123', // Valid password that matches our regex
                'password_confirmation' => 'Password123',
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['email']);
        }
    }

    public function test_registration_name_validation()
    {
        $response = $this->postJson(route('register.submit'), [
            'name' => 'a', // Too short
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    public function test_login_validation()
    {
        $response = $this->postJson(route('login.submit'), [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
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
            // Reset the throttle for each iteration to avoid 429 errors
            $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

            $response = $this->postJson(route('login.submit'), [
                'email' => $email,
                'password' => 'password123',
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['email']);
        }
    }

    public function test_login_password_required()
    {
        /** @var User */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->postJson(route('login.submit'), [
            'email' => $user->email,
            'password' => '',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_password_confirmation_validation()
    {
        $response = $this->postJson(route('register.submit'), [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123',
            'password_confirmation' => 'DifferentPassword123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_email_uniqueness()
    {
        /** @var User */
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->postJson(route('register.submit'), [
            'name' => $this->faker->name,
            'email' => $existingUser->email,
            'phone' => $this->getValidPhoneNumber(),
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_phone_uniqueness()
    {
        /** @var User */
        $existingUser = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->postJson(route('register.submit'), [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $existingUser->phone,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone']);
    }

    public function test_login_attempts_throttling()
    {
        /** @var User */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'phone' => $this->getValidPhoneNumber(),
        ]);

        // Attempt to login 6 times with wrong password
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson(route('login.submit'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be throttled
        $response->assertStatus(429) // Too Many Requests
                ->assertJsonStructure(['message']);
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

        $response = $this->postJson(route('register.submit'), $userData);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Registration successful! You can now login.']);

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
            $response = $this->postJson(route('register.submit'), [
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $phone,
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

            $response->assertStatus(422)
                    ->assertJsonValidationErrors(['phone']);
        }
    }
}
