<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;

class DashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function getValidPhoneNumber(): string
    {
        return '+63' . $this->faker->numerify('#########');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);
    }

    public function test_dashboard_shows_recent_contacts()
    {
        $this->actingAs($this->user);

        // Create some contacts
        $contacts = Contact::factory()
            ->count(5)
            ->forUser($this->user)
            ->create();

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('recentContacts');
    }

    public function test_dashboard_shows_contact_statistics()
    {
        $this->actingAs($this->user);

        // Create categories and contacts
        $category1 = Category::factory()->create(['user_id' => $this->user->id]);
        $category2 = Category::factory()->create(['user_id' => $this->user->id]);

        Contact::factory()
            ->count(3)
            ->forUser($this->user)
            ->forCategory($category1)
            ->create();

        Contact::factory()
            ->count(2)
            ->forUser($this->user)
            ->forCategory($category2)
            ->create();

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('contactsCount', 5);
        $response->assertViewHas('categories');
    }

    public function test_recent_contacts_endpoint_returns_correct_data()
    {
        $this->actingAs($this->user);

        // Create some contacts
        $contacts = Contact::factory()
            ->count(5)
            ->forUser($this->user)
            ->create();

        $response = $this->get(route('recent.contacts'));

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'phone',
                'category_id',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_dashboard_only_shows_user_own_contacts()
    {
        $this->actingAs($this->user);

        // Create contacts for the authenticated user
        $userContacts = Contact::factory()
            ->count(3)
            ->forUser($this->user)
            ->create();

        // Create contacts for another user
        $otherUser = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);
        $otherContacts = Contact::factory()
            ->count(2)
            ->forUser($otherUser)
            ->create();

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('recentContacts', function ($recentContacts) use ($userContacts) {
            return $recentContacts->count() === 3;
        });
    }

    public function test_dashboard_requires_authentication()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_create_category()
    {
        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'Test Category',
            'description' => 'Test Description',
            'type' => 'personal'
        ];

        $response = $this->post(route('categories.store'), $categoryData);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas('categories', [
            'name' => $categoryData['name'],
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_update_category()
    {
        $this->actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updatedData = [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'type' => 'business'
        ];

        $response = $this->put(route('categories.update', $category), $updatedData);

        $response->assertStatus(302);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => $updatedData['name'],
            'user_id' => $this->user->id
        ]);
    }

    public function test_user_can_delete_category()
    {
        $this->actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    public function test_user_cannot_manage_other_users_categories()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create([
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $category = Category::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Unauthorized Update',
            'type' => 'personal'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_view_their_profile()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('profile.show'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.show');
        $response->assertViewHas('user', $this->user);
    }

    public function test_user_can_update_their_profile()
    {
        $this->actingAs($this->user);

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => $this->getValidPhoneNumber()
        ];

        $response = $this->put(route('profile.update'), $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email']
        ]);
    }

    public function test_user_cannot_update_profile_with_invalid_data()
    {
        $this->actingAs($this->user);

        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => 'invalid-phone'
        ];

        $response = $this->put(route('profile.update'), $invalidData);

        $response->assertSessionHasErrors(['name', 'email', 'phone']);
    }

    public function test_user_cannot_create_category_with_invalid_data()
    {
        $this->actingAs($this->user);

        $invalidData = [
            'name' => '',
            'description' => str_repeat('a', 1001),
            'type' => 'invalid-type'
        ];

        $response = $this->post(route('categories.store'), $invalidData);

        $response->assertSessionHasErrors(['name', 'description', 'type']);
    }

    public function test_user_cannot_update_category_with_invalid_data()
    {
        $this->actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $invalidData = [
            'name' => '',
            'description' => str_repeat('a', 1001),
            'type' => 'invalid-type'
        ];

        $response = $this->put(route('categories.update', $category), $invalidData);

        $response->assertSessionHasErrors(['name', 'description', 'type']);
    }

    public function test_category_name_validation()
    {
        $this->actingAs($this->user);

        $invalidNames = [
            '',
            'a',
            str_repeat('a', 256),
        ];

        foreach ($invalidNames as $name) {
            $response = $this->post(route('categories.store'), [
                'name' => $name,
                'description' => 'Valid description',
                'type' => 'personal'
            ]);

            $response->assertSessionHasErrors('name');
        }
    }

    public function test_category_type_validation()
    {
        $this->actingAs($this->user);

        $invalidTypes = [
            '',
            'invalid-type',
            'PERSONAL',
        ];

        foreach ($invalidTypes as $type) {
            $response = $this->post(route('categories.store'), [
                'name' => 'Valid Name',
                'description' => 'Valid description',
                'type' => $type
            ]);

            $response->assertSessionHasErrors('type');
        }
    }

    public function test_profile_update_validation()
    {
        $this->actingAs($this->user);

        $invalidData = [
            'name' => [
                'value' => 'a',
                'error' => 'name'
            ],
            'email' => [
                'value' => 'invalid-email',
                'error' => 'email'
            ],
            'phone' => [
                'value' => 'invalid-phone',
                'error' => 'phone'
            ]
        ];

        foreach ($invalidData as $field => $data) {
            $updateData = [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone
            ];
            $updateData[$field] = $data['value'];

            $response = $this->put(route('profile.update'), $updateData);

            $response->assertSessionHasErrors($data['error']);
        }
    }

    public function test_profile_update_email_uniqueness()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->put(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $otherUser->email,
            'phone' => $this->user->phone
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_profile_update_phone_uniqueness()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->put(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $otherUser->phone
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_category_description_length_validation()
    {
        $this->actingAs($this->user);

        $invalidDescriptions = [
            str_repeat('a', 1001), // Too long
            '', // Empty
        ];

        foreach ($invalidDescriptions as $description) {
            $response = $this->post(route('categories.store'), [
                'name' => 'Valid Name',
                'description' => $description,
                'type' => 'personal'
            ]);

            $response->assertSessionHasErrors('description');
        }
    }

    public function test_category_name_uniqueness_per_user()
    {
        $this->actingAs($this->user);

        // Create initial category
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Category'
        ]);

        // Try to create another category with the same name
        $response = $this->post(route('categories.store'), [
            'name' => $category->name,
            'description' => 'Different description',
            'type' => 'personal'
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_name_allows_special_characters()
    {
        $this->actingAs($this->user);

        $specialNames = [
            'Test Category!',
            'Category @ Work',
            'Category #1',
            'Category & More',
        ];

        foreach ($specialNames as $name) {
            $response = $this->post(route('categories.store'), [
                'name' => $name,
                'description' => 'Valid description',
                'type' => 'personal'
            ]);

            $response->assertStatus(302);
            $this->assertDatabaseHas('categories', [
                'name' => $name,
                'user_id' => $this->user->id
            ]);
        }
    }

    public function test_profile_update_password_validation()
    {
        $this->actingAs($this->user);

        $invalidPasswords = [
            'short', // Too short
            'onlylowercase', // No uppercase
            'ONLYUPPERCASE', // No lowercase
            'NoNumbers', // No numbers
            '12345678', // No letters
        ];

        foreach ($invalidPasswords as $password) {
            $response = $this->put(route('profile.update'), [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'current_password' => 'password123',
                'password' => $password,
                'password_confirmation' => $password
            ]);

            $response->assertSessionHasErrors('password');
        }
    }

    public function test_profile_update_current_password_required()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123'
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_profile_update_current_password_validation()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'current_password' => 'wrong-password',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123'
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_category_update_requires_authentication()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'type' => 'personal'
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_category_delete_requires_authentication()
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('login'));
    }

    public function test_category_cannot_be_deleted_if_has_contacts()
    {
        $this->actingAs($this->user);

        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Create a contact in this category
        Contact::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id
        ]);

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertStatus(422);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
} 