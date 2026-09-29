<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAdminAssignment extends Model
{
    protected $fillable = ['store_id', 'platform_admin_id', 'assigned_by_user_id'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function platformAdmin(): BelongsTo
    {
        return $this->belongsTo(PlatformAdmin::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}
