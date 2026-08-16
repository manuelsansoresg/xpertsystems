<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Commission extends Model
{
    protected $fillable = [
        'seller_id', 'order_id', 'payment_id', 'base_amount',
        'commission_type_snapshot', 'commission_value_snapshot', 'commission_amount',
        'calculation_basis', 'status', 'earned_at', 'available_at', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'base_amount' => 'decimal:2',
            'commission_value_snapshot' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'earned_at' => 'datetime',
            'available_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function payouts(): BelongsToMany
    {
        return $this->belongsToMany(Payout::class)
            ->withPivot('amount')
            ->withTimestamps();
    }
}
