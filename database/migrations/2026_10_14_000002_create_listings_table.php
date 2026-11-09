<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('listings',function(Blueprint $table) { $table->id(); $table->foreignId('category_id')->constrained()->restrictOnDelete(); $table->foreignId('user_id')->constrained()->restrictOnDelete(); $table->string('name'); $table->string('slug')->unique(); $table->text('description'); $table->string('city',100)->index(); $table->string('address'); $table->unsignedInteger('capacity'); $table->decimal('starting_price',10,2); $table->string('image_url',2048)->nullable(); $table->string('phone',30)->nullable(); $table->boolean('is_claimed')->default(false); $table->boolean('is_active')->default(true); $table->timestamps(); $table->index(['category_id','city']); }); }
 public function down(): void { Schema::dropIfExists('listings'); }
};
