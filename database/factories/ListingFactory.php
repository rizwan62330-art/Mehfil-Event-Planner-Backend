<?php
namespace Database\Factories;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class ListingFactory extends Factory {
 public function definition(): array {
  $name=ucwords(fake()->unique()->words(3,true)).' Events';
  return ['category_id'=>Category::factory(), 'user_id'=>User::factory(), 'name'=>$name,'slug'=>Str::slug($name),
    'description'=>fake()->paragraph(3),'city'=>fake()->randomElement(['Lahore','Islamabad','Rawalpindi','Karachi','Peshawar']),
    'address'=>fake()->streetAddress(),'capacity'=>fake()->numberBetween(100,1500),
    'starting_price'=>fake()->numberBetween(50000,450000),'image_url'=>null,'phone'=>fake()->numerify('03#########'),
    'is_claimed'=>false,'is_active'=>true];
 }
}
