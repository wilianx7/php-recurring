<?php

namespace PhpRecurring\Actions\Matchers;

use Carbon\Carbon;
use PhpRecurring\Configurations\NormalizedRecurringConfig;
use PhpRecurring\Enums\WeekdayEnum;

class MatchWeeklyRecurringDateAction
{
    public function execute(NormalizedRecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $currentWeekday = WeekdayEnum::from(strtoupper($currentDate->englishDayOfWeek));
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        $diffInWeeks = (int) $currentDate->diffInWeeks(
            $recurringConfig->getComparisonStartDate()->copy()->startOfWeek(),
            true
        );

        return $diffInWeeks !== 0
            && ($diffInWeeks % $repeatInterval === 0)
            && in_array($currentWeekday, $recurringConfig->getRepeatIn(), true);
    }
}
