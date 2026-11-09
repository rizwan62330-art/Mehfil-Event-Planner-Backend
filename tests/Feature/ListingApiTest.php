<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listing_index_is_paginated_and_supports_filters(): void
    {
        $category = Category::factory()->create();
        Listing::factory()->count(3)->create(['category_id' => $category->id, 'city' => 'Rawalpindi']);
        Listing::factory()->create(['category_id' => $category->id, 'is_active' => false]);

        $response = $this->getJson('/api/listings?per_page=2&city=Rawalpindi');
        $response->assertOk()->assertJsonPath('per_page', 2)->assertJsonPath('total', 3);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_listing_detail_and_categories_are_public(): void
    {
        $listing = Listing::factory()->create();
        $this->getJson('/api/listings/' . $listing->id)->assertOk()->assertJsonPath('id', $listing->id);
        $this->getJson('/api/categories')->assertOk();
    }

    public function test_inactive_listing_is_not_publicly_visible(): void
    {
        $listing = Listing::factory()->create(['is_active' => false]);
        $this->getJson('/api/listings/' . $listing->id)->assertNotFound();
    }

    public function test_guest_cannot_create_listing(): void
    {
        $this->postJson('/api/listings', [])->assertUnauthorized();
    }

    public function test_authenticated_owner_can_create_update_and_delete_listing(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $payload = [
            'category_id' => $category->id,
            'name' => 'Rose Garden Event Hall',
            'description' => 'A spacious event venue suitable for weddings and family celebrations.',
            'city' => 'Rawalpindi', 'address' => 'Main Road, Rawalpindi',
            'capacity' => 500, 'starting_price' => 150000,
        ];

        $this->actingAs($user, 'sanctum')->postJson('/api/listings', $payload)
            ->assertCreated()->assertJsonPath('data.name', $payload['name']);
        $listing = Listing::where('name', $payload['name'])->firstOrFail();

        $this->actingAs($user, 'sanctum')->patchJson('/api/listings/' . $listing->id, ['city' => 'Islamabad'])
            ->assertOk()->assertJsonPath('data.city', 'Islamabad');
        $this->actingAs($user, 'sanctum')->deleteJson('/api/listings/' . $listing->id)->assertOk();
        $this->assertDatabaseMissing('listings', ['id' => $listing->id]);
    }

    public function test_listing_validation_and_ownership_are_enforced(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $listing = Listing::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner, 'sanctum')->postJson('/api/listings', ['name' => 'x'])
            ->assertUnprocessable()->assertJsonValidationErrors(['category_id', 'description', 'city', 'address', 'capacity', 'starting_price']);
        $this->actingAs($otherUser, 'sanctum')->patchJson('/api/listings/' . $listing->id, ['name' => 'Changed Venue'])->assertForbidden();
        $this->actingAs($otherUser, 'sanctum')->deleteJson('/api/listings/' . $listing->id)->assertForbidden();
    }
}
