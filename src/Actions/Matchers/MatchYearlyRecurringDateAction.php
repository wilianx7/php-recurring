<?php

namespace PhpRecurring\Actions\Matchers;

use Carbon\Carbon;
use PhpRecurring\Configurations\NormalizedRecurringConfig;

class MatchYearlyRecurringDateAction
{
    public function execute(NormalizedRecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $currentDay = $currentDate->day;
        $currentMonth = $currentDate->month;
        $lastDayOfMonth = $currentDate->daysInMonth;
        $repeatIn = $recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        $diffInYears = (int) $currentDate->diffInYears(
            $recurringConfig->getComparisonStartDate()->copy()->startOfYear()
        );

        return $diffInYears !== 0
            && ($diffInYears % $repeatInterval === 0)
            && $currentMonth === $repeatIn['month']
            && $this->isValidDayOfMonth($currentDay, $repeatIn['day'], $lastDayOfMonth);
    }

    private function isValidDayOfMonth(int $currentDay, int $targetDay, int $lastDayOfMonth): bool
    {
        return $currentDay === $targetDay || ($currentDay === $lastDayOfMonth && $targetDay > $lastDayOfMonth);
    }
}
