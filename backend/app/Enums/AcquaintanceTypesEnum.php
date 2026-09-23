<?php

namespace App\Enums;

enum AcquaintanceTypesEnum: string
{
    case WITNESS = 'witness';
    case CLOSE_FAMILY = 'close_family';
    case DISTANT_FAMILY = 'distant_family';
    case CLOSE_FRIEND = 'close_friend';
    case DISTANT_FRIEND = 'distant_friend';
    case NEIGHBOR = 'neighbor';
    case PLUS_ONE = 'plus_one';

    public function label(): string
    {
        return match ($this) {
            self::WITNESS => 'Świadek',
            self::CLOSE_FAMILY => 'Rodzina bliska',
            self::DISTANT_FAMILY => 'Rodzina daleka',
            self::CLOSE_FRIEND => 'Znajomy bliski',
            self::DISTANT_FRIEND => 'Znajomy daleki',
            self::NEIGHBOR => 'Sąsiad',
            self::PLUS_ONE => 'Osoba towarzysząca',
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
