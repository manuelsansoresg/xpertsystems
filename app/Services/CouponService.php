<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CouponEvaluationResult;
use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\Customer;

final class CouponService
{
    /**
     * Valida y calcula el descuento de un cupón.
     *
     * Regla comercial documentada:
     * Si coexisten cookie de referido (ej: MARIA20) y cupón (ej: CARLOS10)
     * de vendedores distintos, EL CUPÓN DE VENDEDOR TIENE PRIORIDAD SOBRE EL REFERIDO.
     * El seller atribuido será el del cupón, no el del referido.
     */
    public function evaluate(
        Coupon $coupon,
        Package $package,
        float $subtotal,
        ?Customer $customer = null,
    ): CouponEvaluationResult {
        $errors = $this->validate($coupon, $package, $subtotal, $customer);

        if (!empty($errors)) {
            return CouponEvaluationResult::invalid($errors);
        }

        $discount = $this->calculateDiscount($coupon, $subtotal);
        $total = max(0, $subtotal - $discount);

        return CouponEvaluationResult::valid(
            subtotal: $subtotal,
            discount: $discount,
            total: $total,
            seller_id: $coupon->seller_id,
        );
    }

    /**
     * Valida todas las condiciones del cupón.
     */
    public function validate(
        Coupon $coupon,
        Package $package,
        float $subtotal,
        ?Customer $customer = null,
    ): array {
        $errors = [];

        if (!$coupon->is_active) {
            $errors[] = 'El cupón no está activo.';
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $errors[] = 'El cupón aún no está disponible.';
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            $errors[] = 'El cupón ha expirado.';
        }

        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            $errors[] = 'El cupón ha alcanzado el límite de usos.';
        }

        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            $errors[] = 'El subtotal no cumple el monto mínimo requerido.';
        }

        if ($coupon->scope === \App\Enums\CouponScope::Packages) {
            $packageIds = $coupon->packages()->pluck('packages.id')->toArray();
            if (!in_array($package->id, $packageIds)) {
                $errors[] = 'El cupón no aplica a este paquete.';
            }
        }

        if ($customer && $coupon->usage_limit_per_customer) {
            $customerUsage = $coupon->redemptions()
                ->where('customer_id', $customer->id)
                ->count();

            if ($customerUsage >= $coupon->usage_limit_per_customer) {
                $errors[] = 'Has alcanzado el límite de usos para este cupón.';
            }
        }

        return $errors;
    }

    /**
     * Calcula el monto del descuento.
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = 0.0;

        if ($coupon->discount_type === DiscountType::Percentage) {
            $discount = ($subtotal * $coupon->discount_value) / 100;

            if ($coupon->maximum_discount && $discount > $coupon->maximum_discount) {
                $discount = (float) $coupon->maximum_discount;
            }
        } else {
            $discount = (float) $coupon->discount_value;
        }

        return min($discount, $subtotal);
    }
}
