<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SellerProfile;

final class ReferralCodeGenerator
{
    public function generate(string $name): string
    {
        $base = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $base = substr($base, 0, 5);

        if ($base === '') {
            $base = 'XS';
        }

        do {
            $code = $base . strtoupper(substr(bin2hex(random_bytes(2)), 0, max(1, 6 - strlen($base))));
        } while (SellerProfile::query()->where('referral_code', $code)->exists());

        return $code;
    }

    public function isUnique(string $code, ?int $excludeUserId = null): bool
    {
        $query = SellerProfile::query()->where('referral_code', $code);

        if ($excludeUserId !== null) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        return ! $query->exists();
    }
}
