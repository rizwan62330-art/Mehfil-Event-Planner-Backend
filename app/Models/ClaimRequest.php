<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ClaimRequest extends Model {protected $fillable=['listing_id','user_id','contact_name','contact_email','details','status'];}
