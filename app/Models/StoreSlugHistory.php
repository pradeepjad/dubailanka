<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSlugHistory extends Model
{
    protected $fillable = ['store_id', 'slug', 'changed_by_user_id', 'changed_at'];

    protected function casts(): array
    {
        return ['changed_at' => 'datetime'];
    }

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
}
