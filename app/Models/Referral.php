<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Referral extends Model
{
    protected $fillable = [
        'seller_id', 'customer_id', 'referral_code', 'visitor_token', 'landing_url',
        'referrer_url', 'ip_hash', 'user_agent_hash', 'attributed_at', 'expires_at',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'attributed_at' => 'datetime',
            'expires_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
