<?php
namespace Database\Factories;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class ReviewFactory extends Factory {
 public function definition(): array { return ['listing_id'=>Listing::factory(),'user_id'=>User::factory(),'rating'=>fake()->numberBetween(1,5),'title'=>fake()->sentence(4),'comment'=>fake()->paragraph(),'is_approved'=>true]; }
}
