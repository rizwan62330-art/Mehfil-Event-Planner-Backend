<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Review;
use Illuminate\Http\Request;
class ReviewController extends Controller {
 public function index(Listing $listing) {return response()->json($listing->reviews()->where('is_approved',true)->with('user:id,name')->latest()->get());}
 public function store(Request $request,Listing $listing) {
  abort_unless($listing->is_active,404);
  $data=$request->validate(['rating'=>'required|integer|between:1,5','comment'=>'required|string|min:10|max:3000','title'=>'nullable|string|max:150']);
  $review=Review::updateOrCreate(['listing_id'=>$listing->id,'user_id'=>$request->user()->id],['rating'=>$data['rating'],'title'=>$data['title']??'Venue review','comment'=>$data['comment'],'is_approved'=>false]);
  return response()->json(['message'=>'Review received and awaiting moderation.','review'=>$review],201);
 }
}
