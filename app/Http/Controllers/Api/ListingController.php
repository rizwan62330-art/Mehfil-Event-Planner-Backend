<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::query()
            ->where('is_active', true)
            ->with('category:id,name,slug')
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%"));
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . trim($request->string('city')->toString()) . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        $perPage = min(max($request->integer('per_page', 10), 1), 50);
        return response()->json($query->orderBy('id')->paginate($perPage)->withQueryString());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'min:3', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:listings,slug'],
            'description' => ['required', 'string', 'min:20', 'max:10000'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100000'],
            'starting_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']) . '-' . Str::random(6);
        $data['user_id'] = $request->user()->id;
        $listing = Listing::create($data);
        return response()->json(['message' => 'Listing created successfully.', 'data' => $listing->load('category:id,name,slug')], 201);
    }

    public function show(Listing $listing)
    {
        abort_unless($listing->is_active, 404);
        return response()->json($listing->load('category:id,name,slug'));
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorizeListing($request, $listing);
        $data = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:180'],
            'slug' => ['sometimes', 'required', 'string', 'max:200', Rule::unique('listings', 'slug')->ignore($listing->id)],
            'description' => ['sometimes', 'required', 'string', 'min:20', 'max:10000'],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1', 'max:100000'],
            'starting_price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'image_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $listing->update($data);
        return response()->json(['message' => 'Listing updated successfully.', 'data' => $listing->fresh()->load('category:id,name,slug')]);
    }

    public function destroy(Request $request, Listing $listing)
    {
        $this->authorizeListing($request, $listing);
        $listing->delete();
        return response()->json(['message' => 'Listing deleted successfully.']);
    }

    public function categories()
    {
        return response()->json(Category::orderBy('name')->get(['id', 'name', 'slug']));
    }

    private function authorizeListing(Request $request, Listing $listing): void
    {
        abort_unless($request->user()->role === 'admin' || $listing->user_id === $request->user()->id, 403, 'You are not allowed to manage this listing.');
    }
}
