<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OwnershipAssignment extends Model
{
    protected $fillable=['business_entity_id','store_id','user_id','assigned_by_user_id','assignment_type','assigned_at'];
    protected function casts(): array { return ['assigned_at'=>'datetime']; }
}
