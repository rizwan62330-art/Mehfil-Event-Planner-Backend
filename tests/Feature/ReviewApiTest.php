<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_reviews_endpoint_returns_only_approved_reviews(): void
    {
        $listing = Listing::factory()->create();
        Review::factory()->create(['listing_id' => $listing->id, 'is_approved' => true]);
        Review::factory()->create(['listing_id' => $listing->id, 'is_approved' => false]);

        $response = $this->getJson('/api/listings/' . $listing->id . '/reviews');
        $response->assertOk()->assertJsonPath('total', 1);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $listing = Listing::factory()->create();
        $this->postJson('/api/listings/' . $listing->id . '/reviews', [
            'rating' => 5, 'comment' => 'A wonderful venue for our event.',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_submit_review_and_it_waits_for_moderation(): void
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create();
        $this->actingAs($user, 'sanctum')->postJson('/api/listings/' . $listing->id . '/reviews', [
            'rating' => 5, 'title' => 'Excellent venue', 'comment' => 'The staff and facilities were excellent.',
        ])->assertCreated()->assertJsonPath('data.is_approved', false);
        $this->assertDatabaseHas('reviews', ['listing_id' => $listing->id, 'user_id' => $user->id, 'rating' => 5]);
    }

    public function test_review_validation_rejects_invalid_rating_and_short_comment(): void
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create();
        $this->actingAs($user, 'sanctum')->postJson('/api/listings/' . $listing->id . '/reviews', [
            'rating' => 8, 'comment' => 'short',
        ])->assertUnprocessable()->assertJsonValidationErrors(['rating', 'comment']);
    }

    public function test_review_owner_can_update_and_delete_but_other_user_cannot(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $owner->id, 'is_approved' => true]);

        $this->actingAs($other, 'sanctum')->patchJson('/api/reviews/' . $review->id, ['rating' => 4])->assertForbidden();
        $this->actingAs($owner, 'sanctum')->patchJson('/api/reviews/' . $review->id, ['rating' => 4])
            ->assertOk()->assertJsonPath('data.rating', 4)->assertJsonPath('data.is_approved', false);
        $this->actingAs($owner, 'sanctum')->deleteJson('/api/reviews/' . $review->id)->assertOk();
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
