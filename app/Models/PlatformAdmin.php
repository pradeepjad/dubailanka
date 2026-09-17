<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PlatformAdmin extends Model { protected $fillable=['user_id','is_super_admin','status']; protected function casts(): array{return ['is_super_admin'=>'boolean'];} public function user():BelongsTo{return $this->belongsTo(User::class);} }
