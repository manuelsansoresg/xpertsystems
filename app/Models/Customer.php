<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'seller_id', 'first_name', 'last_name', 'business_name', 'email', 'phone',
        'country', 'currency', 'referral_code', 'source', 'notes',
        'first_purchase_at', 'last_purchase_at',
    ];

    protected function casts(): array
    {
        return [
            'first_purchase_at' => 'datetime',
            'last_purchase_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
