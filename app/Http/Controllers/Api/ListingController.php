<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Category;
class ListingController extends Controller {
 public function index() {
  return response()->json(Listing::where('is_active',true)->with('category:id,name,slug')->withCount(['reviews'=>fn($q)=>$q->where('is_approved',true)])->withAvg(['reviews'=>fn($q)=>$q->where('is_approved',true)],'rating')->orderBy('id')->get());
 }
 public function categories(){return response()->json(Category::orderBy('name')->get(['id','name','slug']));}
 public function show(Listing $listing){abort_unless($listing->is_active,404);return response()->json($listing->load('category:id,name,slug'));}
}
