<?php

namespace PhpRecurring\Actions\Matchers;

use Carbon\Carbon;
use PhpRecurring\Configurations\NormalizedRecurringConfig;

class MatchDailyRecurringDateAction
{
    public function execute(NormalizedRecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $repeatInterval = $recurringConfig->getFrequencyInterval();
        $diffInDays = (int) $currentDate->diffInDays($recurringConfig->getComparisonStartDate());

        return $diffInDays !== 0 && ($diffInDays % $repeatInterval === 0);
    }
}
