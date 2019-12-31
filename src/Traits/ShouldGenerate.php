<?php


namespace PhpRecurring\Traits;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\RecurringConfig;
use Tightenco\Collect\Support\Collection;

trait ShouldGenerate
{
    protected function shouldGenerate(RecurringConfig $recurringConfig, Carbon $currentDate, Collection $datesCollection): bool
    {
        if ($recurringConfig && $recurringConfig->getLastRepeatedDate()) {
            $currentGeneratedCount = count($datesCollection);
            $lastRepeatedDate = $recurringConfig->getLastRepeatedDate();

            switch ($recurringConfig->getFrequencyEndType()) {
                case FrequencyEndTypeEnum::NEVER():
                    return $lastRepeatedDate->year < $currentDate->year;
                case FrequencyEndTypeEnum::IN():
                    return ($lastRepeatedDate < $recurringConfig->getFrequencyEndValue() && $lastRepeatedDate->year < $currentDate->year);
                case FrequencyEndTypeEnum::AFTER():
                    if ($recurringConfig->getRepeatedCount()) {
                        return (($recurringConfig->getRepeatedCount() + $currentGeneratedCount) < $recurringConfig->getFrequencyEndValue() && $lastRepeatedDate->year < $currentDate->year);
                    }
            }
        }

        return true;
    }
}