<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StoreStatusHistory extends Model { protected $fillable=['store_id','actor_user_id','from_status','to_status','note']; public function store():BelongsTo{return $this->belongsTo(Store::class);} public function actor():BelongsTo{return $this->belongsTo(User::class,'actor_user_id');} }
