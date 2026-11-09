<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  foreach (range(1,5) as $i) {
   User::firstOrCreate(['email'=>"owner{$i}@mehfil.test"],['name'=>"Venue Owner {$i}",'password'=>'password','role'=>'owner']);
  }
  foreach (range(1,5) as $i) {
   User::firstOrCreate(['email'=>"guest{$i}@mehfil.test"],['name'=>"Demo Guest {$i}",'password'=>'password','role'=>'customer']);
  }
  $this->call([CategorySeeder::class,ListingSeeder::class,ReviewSeeder::class]);
 }
}
