<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreUser extends Model
{
    protected $fillable = ['store_id', 'user_id', 'role', 'is_primary_owner'];

    protected function casts(): array
    {
        return ['is_primary_owner' => 'boolean'];
    }

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
