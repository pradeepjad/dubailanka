<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessEntity extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_PERSONAL = 'personal_business';
    public const TYPE_REGISTERED = 'registered_company';

    protected $fillable = [
        'type',
        'legal_name',
        'trading_name',
        'registration_number',
        'country_code',
        'address',
        'phone',
        'whatsapp',
        'email',
        'status',
    ];

    public function memberships(): HasMany
    {
        return $this->hasMany(BusinessEntityUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_entity_users')
            ->withPivot(['role', 'is_primary_owner'])
            ->withTimestamps();
    }
}
