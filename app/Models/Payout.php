<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Payout extends Model
{
    protected $fillable = [
        'seller_id', 'recorded_by', 'amount', 'payment_method', 'reference',
        'notes', 'status', 'paid_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function commissions(): BelongsToMany
    {
        return $this->belongsToMany(Commission::class)
            ->withPivot('amount')
            ->withTimestamps();
    }
}
