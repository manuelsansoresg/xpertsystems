<?php

declare(strict_types=1);

namespace App\Enums;

enum CouponScope: string
{
    case Global = 'global';
    case Packages = 'packages';

    public function label(): string
    {
        return match ($this) {
            self::Global => 'Todos los paquetes',
            self::Packages => 'Paquetes específicos',
        };
    }
}
