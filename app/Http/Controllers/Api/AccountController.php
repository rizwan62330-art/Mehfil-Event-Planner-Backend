<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ClaimRequest;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AccountController extends Controller {
 public function favorites(Request $request) {return response()->json($request->user()->belongsToMany(Listing::class,'favorites')->pluck('listings.id'));}
 public function toggleFavorite(Request $request,Listing $listing) {
  abort_unless($listing->is_active,404);
  $relation=$request->user()->belongsToMany(Listing::class,'favorites');
  $saved=$relation->where('listings.id',$listing->id)->exists();
  if($saved)$relation->detach($listing->id); else $relation->attach($listing->id);
  return response()->json(['saved'=>!$saved]);
 }
 public function claim(Request $request) {
  $data=$request->validate(['listing_id'=>'required|exists:listings,id','name'=>'required|string|max:120','email'=>'required|email|max:255','details'=>'required|string|min:10|max:3000']);
  $listing=Listing::findOrFail($data['listing_id']);
  abort_if(!$listing->is_active||$listing->is_claimed,422,'This listing is unavailable for claiming.');
  $claim=ClaimRequest::updateOrCreate(['listing_id'=>$listing->id,'user_id'=>$request->user()->id],['contact_name'=>$data['name'],'contact_email'=>$data['email'],'details'=>$data['details'],'status'=>'pending']);
  return response()->json(['message'=>'Claim submitted for review.','claim'=>$claim],201);
 }
}
