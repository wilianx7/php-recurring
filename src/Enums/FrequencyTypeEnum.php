<?php

namespace PhpRecurring\Enums;

enum FrequencyTypeEnum
{
    case DAY;
    case WEEK;
    case MONTH;
    case YEAR;

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