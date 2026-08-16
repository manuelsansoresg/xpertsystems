<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class SellerProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'referral_code',
        'commission_type',
        'commission_value',
        'status',
        'payment_method',
        'payment_details',
        'notes',
    ];

    protected $hidden = ['payment_details'];

    protected function casts(): array
    {
        return [
            'commission_value' => 'decimal:2',
            'payment_details' => 'encrypted',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
