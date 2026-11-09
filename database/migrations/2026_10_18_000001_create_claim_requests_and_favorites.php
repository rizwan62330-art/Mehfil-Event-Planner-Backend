<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('claim_requests',function(Blueprint $table){$table->id();$table->foreignId('listing_id')->constrained()->cascadeOnDelete();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->string('contact_name');$table->string('contact_email');$table->text('details');$table->string('status')->default('pending');$table->timestamps();$table->unique(['listing_id','user_id']);});
  Schema::create('favorites',function(Blueprint $table){$table->id();$table->foreignId('listing_id')->constrained()->cascadeOnDelete();$table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->timestamps();$table->unique(['listing_id','user_id']);});
 }
 public function down():void{Schema::dropIfExists('favorites');Schema::dropIfExists('claim_requests');}
};
