<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerOnboardingHandover extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'ownership_mode', 'user_id', 'owner_name', 'owner_email', 'business_entity_id',
        'status', 'handed_over_by_user_id', 'completed_at',
    ];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function businessEntity(): BelongsTo { return $this->belongsTo(BusinessEntity::class); }
    public function handedOverBy(): BelongsTo { return $this->belongsTo(User::class, 'handed_over_by_user_id'); }
}
