<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Business entity membership records for this account.
     */
    public function businessEntityMemberships(): HasMany
    {
        return $this->hasMany(BusinessEntityUser::class);
    }

    /**
     * Business entities this account can access.
     */
    public function businessEntities(): BelongsToMany
    {
        return $this->belongsToMany(BusinessEntity::class, 'business_entity_users')
            ->withPivot(['role', 'is_primary_owner'])
            ->withTimestamps();
    }

    /**
     * Store membership records for this account.
     */
    public function storeMemberships(): HasMany
    {
        return $this->hasMany(StoreUser::class);
    }

    /**
     * Stores this account can access.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'store_users')
            ->withPivot(['role', 'is_primary_owner'])
            ->withTimestamps();
    }

    public function platformAdmin(): HasOne
    {
        return $this->hasOne(PlatformAdmin::class);
    }

    /**
     * Determine whether the account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Determine whether the account is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Determine whether the account is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Determine whether the account currently has a password.
     */
    public function hasPassword(): bool
    {
        return ! empty($this->password);
    }
}
