<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Listing $listing)
    {
        abort_unless($listing->is_active, 404);
        return response()->json($listing->reviews()->where('is_approved', true)
            ->with('user:id,name')->latest()->paginate(10));
    }

    public function store(Request $request, Listing $listing)
    {
        abort_unless($listing->is_active, 404);
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:10', 'max:3000'],
            'title' => ['nullable', 'string', 'max:150'],
        ]);
        $review = Review::updateOrCreate(
            ['listing_id' => $listing->id, 'user_id' => $request->user()->id],
            ['rating' => $data['rating'], 'title' => $data['title'] ?? 'Venue review', 'comment' => $data['comment'], 'is_approved' => false]
        );
        return response()->json(['message' => 'Review received and awaiting moderation.', 'data' => $review], 201);
    }

    public function update(Request $request, Review $review)
    {
        abort_unless($review->user_id === $request->user()->id || $request->user()->role === 'admin', 403, 'You are not allowed to update this review.');
        $data = $request->validate([
            'rating' => ['sometimes', 'required', 'integer', 'between:1,5'],
            'comment' => ['sometimes', 'required', 'string', 'min:10', 'max:3000'],
            'title' => ['sometimes', 'nullable', 'string', 'max:150'],
        ]);
        $review->update(array_merge($data, ['is_approved' => false]));
        return response()->json(['message' => 'Review updated and returned for moderation.', 'data' => $review->fresh()]);
    }

    public function destroy(Request $request, Review $review)
    {
        abort_unless($review->user_id === $request->user()->id || $request->user()->role === 'admin', 403, 'You are not allowed to delete this review.');
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully.']);
    }
}
