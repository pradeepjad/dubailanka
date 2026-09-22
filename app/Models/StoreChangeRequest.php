<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreChangeRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_NEEDS_CHANGES = 'needs_changes';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'store_id', 'requested_by_user_id', 'status', 'proposed_name', 'proposed_slug',
        'proposed_country_code', 'proposed_city', 'proposed_address', 'admin_note',
        'reviewed_by_user_id', 'review_started_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'review_started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function store(): BelongsTo { return $this->belongsTo(Store::class); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class, 'requested_by_user_id'); }
    public function reviewedBy(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by_user_id'); }
}
