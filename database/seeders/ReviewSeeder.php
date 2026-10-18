<?php
namespace Database\Seeders;
use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
class ReviewSeeder extends Seeder {
 public function run(): void {
  $reviewers=User::where('role','customer')->orderBy('id')->get();
  foreach (Listing::orderBy('id')->get() as $listing) {
   foreach ($reviewers->take(2) as $user) {
    Review::updateOrCreate(['listing_id'=>$listing->id,'user_id'=>$user->id],[
      'rating'=>fake()->numberBetween(3,5), 'title'=>fake()->sentence(4),
      'comment'=>fake()->paragraph(), 'is_approved'=>true,
    ]);
   }
  }
 }
}
