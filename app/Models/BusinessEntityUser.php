<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessEntityUser extends Model
{
    protected $fillable = [
        'business_entity_id',
        'user_id',
        'role',
        'is_primary_owner',
    ];

    protected function casts(): array
    {
        return [
            'is_primary_owner' => 'boolean',
        ];
    }

    public function businessEntity(): BelongsTo
    {
        return $this->belongsTo(BusinessEntity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
