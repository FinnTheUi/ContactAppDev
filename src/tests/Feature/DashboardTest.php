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

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name', 'email', 'phone']]);
    }
} 