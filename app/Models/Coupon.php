<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'scope',
        'seller_id',
        'starts_at',
        'expires_at',
        'usage_limit',
        'usage_limit_per_customer',
        'times_used',
        'minimum_amount',
        'maximum_discount',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'discount_type' => DiscountType::class,
        'scope' => CouponScope::class,
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'times_used' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'coupon_package')
            ->withTimestamps();
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function computeStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'scheduled';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return 'exhausted';
        }

        return 'active';
    }

    public function statusLabel(): string
    {
        return match ($this->computeStatus()) {
            'active' => 'Activo',
            'scheduled' => 'Programado',
            'expired' => 'Expirado',
            'exhausted' => 'Agotado',
            'inactive' => 'Inactivo',
        };
    }

    public function statusColor(): string
    {
        return match ($this->computeStatus()) {
            'active' => 'emerald',
            'scheduled' => 'blue',
            'expired' => 'amber',
            'exhausted' => 'red',
            'inactive' => 'slate',
        };
    }

    public function discountDisplay(): string
    {
        if ($this->discount_type === DiscountType::Percentage) {
            return $this->discount_value . '%';
        }

        return '$' . number_format((float) $this->discount_value, 2) . ' MXN';
    }

    public function scopeDisplay(): string
    {
        if ($this->scope === CouponScope::Global) {
            return 'Todos los paquetes';
        }

        $count = $this->packages()->count();

        if ($count <= 3) {
            return $this->packages()->pluck('name')->implode(' + ');
        }

        return $count . ' paquetes';
    }
}
