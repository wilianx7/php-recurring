<?php


namespace PhpRecurring\Enums;

enum WeekdayEnum
{
    case SUNDAY;
    case MONDAY;
    case TUESDAY;
    case WEDNESDAY;
    case THURSDAY;
    case FRIDAY;
    case SATURDAY;

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