<?php

namespace PhpRecurring\Actions\Matchers;

use Carbon\Carbon;
use PhpRecurring\Configurations\NormalizedRecurringConfig;

class MatchMonthlyRecurringDateAction
{
    public function execute(NormalizedRecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $currentDay = $currentDate->day;
        $lastDayOfMonth = $currentDate->daysInMonth;
        $repeatIn = $recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        $diffInMonths = (int) $currentDate->diffInMonths(
            $recurringConfig->getComparisonStartDate()->copy()->startOfMonth()
        );

        return $diffInMonths !== 0
            && ($diffInMonths % $repeatInterval === 0)
            && $this->isValidDayOfMonth($currentDay, $repeatIn, $lastDayOfMonth);
    }

    private function isValidDayOfMonth(int $currentDay, int $targetDay, int $lastDayOfMonth): bool
    {
        return $currentDay === $targetDay
            || ($currentDay === $lastDayOfMonth && $targetDay > $lastDayOfMonth);
    }
}
