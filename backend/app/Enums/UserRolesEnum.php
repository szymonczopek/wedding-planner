<?php

namespace App\Enums;

enum UserRolesEnum: string
{
    case GROOM = 'groom';
    case BRIDE = 'bride';
    case WEDDING_PLANNER = 'wedding_planner';

    public function label(): string
    {
        return match ($this) {
            self::GROOM => 'Pan młody',
            self::BRIDE => 'Pani młoda',
            self::WEDDING_PLANNER => 'Wedding Planner',
        };
    }

    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            function ($carry, $case) {
                $carry[$case->value] = $case->label();
                return $carry;
            },
            []
        );
    }
}
