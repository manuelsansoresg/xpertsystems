<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'short_description', 'price', 'currency', 'price_type',
        'direct_checkout', 'requires_quote', 'deposit_percentage', 'featured',
        'badge', 'features', 'note', 'sort_order', 'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'direct_checkout' => 'boolean',
            'requires_quote' => 'boolean',
            'deposit_percentage' => 'integer',
            'featured' => 'boolean',
            'features' => 'array',
            'active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getDepositAmountAttribute(): ?float
    {
        return $this->direct_checkout
            ? round((float) $this->price * ((int) $this->deposit_percentage / 100), 2)
            : null;
    }
}
