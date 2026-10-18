<?php
namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;
class CategorySeeder extends Seeder {
 public function run(): void {
  foreach ([
   ['name'=>'Wedding Halls','slug'=>'wedding-halls','description'=>'Traditional halls for wedding events'],
   ['name'=>'Marquees','slug'=>'marquees','description'=>'Large marquee and banquet setups'],
   ['name'=>'Farmhouses','slug'=>'farmhouses','description'=>'Outdoor event properties and lawns'],
   ['name'=>'Hotels','slug'=>'hotels','description'=>'Hotels with event and banquet facilities'],
   ['name'=>'Rooftop Venues','slug'=>'rooftop-venues','description'=>'Open-air rooftops for intimate functions'],
  ] as $category) { Category::updateOrCreate(['slug'=>$category['slug']],$category); }
 }
}
