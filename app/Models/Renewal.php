<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Renewal extends Model
{
    protected $fillable = [
        'order_id', 'customer_id', 'package_id', 'service_name', 'renewal_date',
        'amount', 'currency', 'status', 'reminder_status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'renewal_date' => 'date',
            'amount' => 'decimal:2',
            'reminder_status' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
