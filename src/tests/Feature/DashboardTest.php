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
} 