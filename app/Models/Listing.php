<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Listing extends Model {
 use HasFactory;
 protected $fillable=['category_id','user_id','name','slug','description','city','address','capacity','starting_price','image_url','phone','is_claimed','is_active'];
 protected $casts=['capacity'=>'integer','starting_price'=>'decimal:2','is_claimed'=>'boolean','is_active'=>'boolean'];
 public function category(): BelongsTo { return $this->belongsTo(Category::class); }
 public function user(): BelongsTo { return $this->belongsTo(User::class); }
 public function reviews(): HasMany { return $this->hasMany(Review::class); }
}
