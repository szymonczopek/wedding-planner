<?php

namespace App\Enums;

enum DietsEnum: string
{
    case VEGETARIAN = 'vegetarian';
    case VEGAN = 'vegan';
    case KETO = 'keto';
    case LIGHT = 'light';
    case GLUTEN_FREE = 'gluten_free';
    case LACTOSE_FREE = 'lactose_free';
    case NUT_FREE = 'nut_free';
    case FISH_AND_SEAFOOD_FREE = 'fish_and_seafood_free';
    case HALAL = 'halal';
    case KOSHER = 'kosher';
    case KIDS_MENU = 'kids_menu';

    public function label(): string
    {
        return match ($this) {
            self::VEGETARIAN => 'Wegetariańska',
            self::VEGAN => 'Wegańska',
            self::KETO => 'Keto',
            self::LIGHT => 'Lekkostrawna',
            self::GLUTEN_FREE => 'Bezglutenowa',
            self::LACTOSE_FREE => 'Bez laktozy',
            self::NUT_FREE => 'Bez orzechów',
            self::FISH_AND_SEAFOOD_FREE => 'Bez ryb i owoców morza',
            self::HALAL => 'Halal',
            self::KOSHER => 'Koszerna',
            self::KIDS_MENU => 'Menu dziecięce',
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
