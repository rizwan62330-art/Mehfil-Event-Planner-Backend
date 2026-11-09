<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('reviews',function(Blueprint $table) { $table->id(); $table->foreignId('listing_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->unsignedTinyInteger('rating'); $table->string('title',150); $table->text('comment'); $table->boolean('is_approved')->default(false); $table->timestamps(); $table->unique(['listing_id','user_id']); }); }
 public function down(): void { Schema::dropIfExists('reviews'); }
};
