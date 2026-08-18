<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CouponRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'customer_id',
        'sale_id',
        'seller_id',
        'code_snapshot',
        'discount_type_snapshot',
        'discount_value_snapshot',
        'discount_amount',
        'redeemed_at',
    ];

    protected $casts = [
        'discount_value_snapshot' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'redeemed_at' => 'datetime',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
