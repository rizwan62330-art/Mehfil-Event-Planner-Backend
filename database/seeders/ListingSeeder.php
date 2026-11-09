<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class ListingSeeder extends Seeder {
 public function run(): void {
  $venues=[
   ['Royal Orchid Banquet','Lahore','wedding-halls',600,180000],
   ['Pearl Garden Marquee','Islamabad','marquees',1000,290000],
   ['Rosewood Farmhouse','Rawalpindi','farmhouses',450,150000],
   ['Grand Sapphire Hotel','Karachi','hotels',800,350000],
   ['Skyline Terrace','Islamabad','rooftop-venues',180,95000],
   ['Golden Palace Wedding Hall','Peshawar','wedding-halls',750,220000],
   ['Moonlight Marquee','Lahore','marquees',1200,420000],
   ['Olive Grove Farmhouse','Islamabad','farmhouses',350,130000],
   ['Emerald Suites Banquet','Rawalpindi','hotels',500,250000],
   ['The Sunset Rooftop','Karachi','rooftop-venues',220,110000],
  ];
  $owners=User::where('role','owner')->get();
  foreach ($venues as $i=>$v) {
   [$name,$city,$slug,$capacity,$price]=$v;
   Listing::updateOrCreate(['slug'=>Str::slug($name)],[
    'category_id'=>Category::where('slug',$slug)->firstOrFail()->id,
    'user_id'=>$owners[$i % $owners->count()]->id,
    'name'=>$name,'description'=>fake()->paragraph(3), 'city'=>$city,
    'address'=>fake()->streetAddress().', '.$city.', Pakistan',
    'capacity'=>$capacity,'starting_price'=>$price,'phone'=>fake()->numerify('03#########'),
    'image_url'=>null,'is_claimed'=>false,'is_active'=>true,
   ]);
  }
 }
}
