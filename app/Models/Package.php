<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'short_description', 'long_description',
        'price', 'currency', 'price_type',
        'direct_checkout', 'requires_quote', 'deposit_percentage',
        'featured', 'is_featured', 'badge',
        'features', 'note', 'sort_order', 'active',
        'button_text', 'public_visibility',
        'renewal_required', 'renewal_enabled',
        'renewal_price', 'renewal_period', 'renewal_after_months',
        'renewal_includes', 'renewal_public_text', 'show_renewal_price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'direct_checkout' => 'boolean',
            'requires_quote' => 'boolean',
            'deposit_percentage' => 'integer',
            'featured' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'array',
            'active' => 'boolean',
            'public_visibility' => 'boolean',
            'renewal_required' => 'boolean',
            'renewal_enabled' => 'boolean',
            'renewal_price' => 'decimal:2',
            'renewal_after_months' => 'integer',
            'renewal_includes' => 'array',
            'show_renewal_price' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->where('public_visibility', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function featureItems(): HasMany
    {
        return $this->hasMany(PackageFeature::class)->orderBy('sort_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getDepositAmountAttribute(): ?float
    {
        return $this->direct_checkout
            ? (float) $this->price
            : null;
    }

    public function getRenewalIncludesArrayAttribute(): array
    {
        return $this->renewal_includes ?? [];
    }
}
