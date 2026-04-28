<?php

namespace PhpRecurring\Actions;

use Carbon\Carbon;
use PhpRecurring\Configurations\NormalizedRecurringConfig;

class ShouldGenerateDateAction
{
    /**
     * @param Carbon[] $generatedDates
     */
    public function execute(
        NormalizedRecurringConfig $recurringConfig,
        Carbon $currentDate,
        array $generatedDates
    ): bool {
        $lastRepeatedDate = $recurringConfig->getLastRepeatedDate();

        if (!$lastRepeatedDate) {
            return true;
        }

        if ($currentDate->lte($lastRepeatedDate)) {
            return false;
        }

        $frequencyEndDate = $recurringConfig->getFrequencyEndDate();

        if ($frequencyEndDate && $lastRepeatedDate->gte($frequencyEndDate)) {
            return false;
        }

        $occurrenceLimit = $recurringConfig->getOccurrenceLimit();

        if ($occurrenceLimit !== null) {
            return ($recurringConfig->getRepeatedCount() + count($generatedDates)) < $occurrenceLimit;
        }

        return true;
    }
}
