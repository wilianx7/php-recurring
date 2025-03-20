<?php


namespace PhpRecurring\Traits;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\RecurringConfig;
use Illuminate\Support\Collection;

trait ShouldGenerate
{
    protected function shouldGenerate(
        RecurringConfig $recurringConfig,
        Carbon          $currentDate,
        Collection      $datesCollection
    ): bool {
        if ($recurringConfig->getLastRepeatedDate()) {
            $currentGeneratedCount = $datesCollection->count();
            $lastRepeatedDate = $recurringConfig->getLastRepeatedDate();

            switch ($recurringConfig->getFrequencyEndType()) {
                case FrequencyEndTypeEnum::NEVER:
                    return $lastRepeatedDate->year < $currentDate->year;

                case FrequencyEndTypeEnum::IN:
                    return $lastRepeatedDate < $recurringConfig->getFrequencyEndValue()
                        && $lastRepeatedDate->year < $currentDate->year;

                case FrequencyEndTypeEnum::AFTER:
                    if ($recurringConfig->getRepeatedCount()) {
                        $totalGeneratedCount = $recurringConfig->getRepeatedCount() + $currentGeneratedCount;

                        return $totalGeneratedCount < $recurringConfig->getFrequencyEndValue()
                            && $lastRepeatedDate->year < $currentDate->year;
                    }
            }
        }

        return true;
    }
}