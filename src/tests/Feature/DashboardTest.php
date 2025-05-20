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
    protected array $headers = ['Accept' => 'application/json'];

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
        $this->withoutExceptionHandling();
        
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        
        $this->getJson(route('dashboard'), $this->headers);
    }

    public function test_user_can_create_category()
    {
        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'Test Category',
            'type' => 'personal'
        ];

        $response = $this->postJson(route('categories.store'), $categoryData);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Category added successfully!']);

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
            'type' => 'business'
        ];

        $response = $this->putJson(route('categories.update', $category), $updatedData);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Category updated successfully!']);

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

        $response = $this->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(200)
                ->assertJson(['message' => 'Category deleted successfully.']);

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

        $response = $this->putJson(route('categories.update', $category), [
            'name' => 'Unauthorized Update',
            'type' => 'personal'
        ]);

        $response->assertStatus(403)
                ->assertJson(['error' => 'Unauthorized action.']);
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

        $response = $this->putJson(route('profile.update'), $updatedData, $this->headers);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $updatedData['name'],
            'email' => $updatedData['email']
        ]);
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

            $response = $this->putJson(route('profile.update'), $updateData, $this->headers);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([$data['error']]);
        }
    }

    public function test_profile_update_email_uniqueness()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->putJson(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $otherUser->email,
            'phone' => $this->user->phone
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_profile_update_phone_uniqueness()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'phone' => $this->getValidPhoneNumber(),
        ]);

        $response = $this->putJson(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $otherUser->phone
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
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
            $response = $this->putJson(route('profile.update'), [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'current_password' => 'password123',
                'password' => $password,
                'password_confirmation' => $password
            ], $this->headers);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        }
    }

    public function test_profile_update_current_password_required()
    {
        $this->actingAs($this->user);

        $response = $this->putJson(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123'
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_profile_update_current_password_validation()
    {
        $this->actingAs($this->user);

        $response = $this->putJson(route('profile.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'current_password' => 'wrong-password',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123'
        ], $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_category_update_requires_authentication()
    {
        $this->withoutExceptionHandling();
        
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        
        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->putJson(route('categories.update', $category), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'type' => 'personal'
        ], $this->headers);
    }

    public function test_category_delete_requires_authentication()
    {
        $this->withoutExceptionHandling();
        
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        
        $category = Category::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->deleteJson(route('categories.destroy', $category), [], $this->headers);
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

        $response = $this->deleteJson(route('categories.destroy', $category));

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Cannot delete a category with associated contacts.']);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}