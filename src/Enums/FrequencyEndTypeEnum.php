<?php


namespace PhpRecurring\Enums;

enum FrequencyEndTypeEnum
{
    case NEVER;
    case IN;
    case AFTER;

    public static function from(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name == $value) {
                return $case;
            }
        }

        return null;
    }
}