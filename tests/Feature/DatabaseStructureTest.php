<?php
namespace Tests\Feature;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
class DatabaseStructureTest extends TestCase {
 use RefreshDatabase;
 public function test_expected_tables_and_columns_exist(): void {
  $this->assertTrue(Schema::hasTable('users'));
  $this->assertTrue(Schema::hasColumns('categories',['id','name','slug']));
  $this->assertTrue(Schema::hasColumns('listings',['id','user_id','category_id','city','starting_price']));
  $this->assertTrue(Schema::hasColumns('reviews',['id','user_id','listing_id','rating']));
 }
 public function test_seeders_populate_related_records(): void {
  $this->seed();
  $this->assertSame(5,Category::count());
  $this->assertSame(10,Listing::count());
  $this->assertSame(20,Review::count());
  $listing=Listing::with(['user','category','reviews'])->firstOrFail();
  $this->assertNotNull($listing->user);
  $this->assertNotNull($listing->category);
  $this->assertCount(2,$listing->reviews);
 }
}
