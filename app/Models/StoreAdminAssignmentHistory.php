<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAdminAssignmentHistory extends Model
{
    protected $fillable = [
        'store_id', 'from_platform_admin_id', 'to_platform_admin_id',
        'assigned_by_user_id', 'assigned_at',
    ];

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime'];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
