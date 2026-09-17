<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_NEEDS_CHANGES = 'needs_changes';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'business_entity_id', 'name', 'slug', 'description', 'country_code',
        'city', 'address', 'phone', 'whatsapp', 'email', 'website',
        'logo_path', 'cover_path', 'business_hours', 'status',
    ];

    protected $appends = ['logo_url', 'cover_url'];

    public function businessEntity(): BelongsTo
    {
        return $this->belongsTo(BusinessEntity::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(StoreUser::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StoreStatusHistory::class)->latest();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'store_users')
            ->withPivot(['role', 'is_primary_owner'])
            ->withTimestamps();
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }
}
