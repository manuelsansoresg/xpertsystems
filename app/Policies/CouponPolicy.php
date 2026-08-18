<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

final class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Coupon $coupon): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('seller')) {
            return $coupon->seller_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Coupon $coupon): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->hasRole('admin');
    }
}
