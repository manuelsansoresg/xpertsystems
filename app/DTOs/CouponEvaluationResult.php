<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CouponEvaluationResult
{
    public function __construct(
        public bool $valid,
        public array $errors,
        public ?float $subtotal,
        public ?float $discount,
        public ?float $total,
        public ?int $seller_id,
    ) {}

    public static function invalid(array $errors): self
    {
        return new self(
            valid: false,
            errors: $errors,
            subtotal: null,
            discount: null,
            total: null,
            seller_id: null,
        );
    }

    public static function valid(
        float $subtotal,
        float $discount,
        float $total,
        ?int $seller_id,
    ): self {
        return new self(
            valid: true,
            errors: [],
            subtotal: $subtotal,
            discount: $discount,
            total: $total,
            seller_id: $seller_id,
        );
    }
}
