<?php

namespace PhpRecurring\Actions\Configurations;

use Carbon\Carbon;
use PhpRecurring\Actions\GenerateEndDateAction;
use PhpRecurring\Configurations\NormalizedRecurringConfig;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\RecurringConfig;

class NormalizeRecurringConfigAction
{
    public function __construct(
        private GenerateEndDateAction $generateEndDateAction = new GenerateEndDateAction()
    ) {
    }

    public function execute(RecurringConfig $recurringConfig): NormalizedRecurringConfig
    {
        $originalStartDate = $recurringConfig->getStartDate()->copy();
        $includeStartDate = (bool) $recurringConfig->getIncludeStartDate();

        $comparisonStartDate = $includeStartDate
            ? $this->shiftStartDateForComparison($recurringConfig)
            : $originalStartDate->copy();

        $frequencyEndDate = $recurringConfig->getFrequencyEndType() === FrequencyEndTypeEnum::IN
            ? $recurringConfig->getFrequencyEndValue()?->copy()
            : null;

        return new NormalizedRecurringConfig(
            originalStartDate: $originalStartDate,
            comparisonStartDate: $comparisonStartDate,
            endDate: $recurringConfig->getEndDate()?->copy() ?? $this->generateImplicitEndDate($recurringConfig),
            frequencyType: $recurringConfig->getFrequencyType(),
            frequencyInterval: $recurringConfig->getFrequencyInterval(),
            repeatIn: $this->normalizeRepeatIn($recurringConfig),
            frequencyEndType: $recurringConfig->getFrequencyEndType(),
            frequencyEndDate: $frequencyEndDate,
            lastRepeatedDate: $recurringConfig->getLastRepeatedDate()?->copy(),
            repeatedCount: $recurringConfig->getRepeatedCount() ?? 0,
            includeStartDate: $includeStartDate,
            occurrenceLimit: $recurringConfig->getFrequencyEndType() === FrequencyEndTypeEnum::AFTER
                ? $recurringConfig->getFrequencyEndValue()
                : null,
            exceptDates: array_map(
                fn (Carbon $exceptDate): Carbon => $exceptDate->copy(),
                $recurringConfig->getExceptDates() ?? []
            ),
        );
    }

    private function generateImplicitEndDate(RecurringConfig $recurringConfig): ?Carbon
    {
        return $this->generateEndDateAction->execute(
            $recurringConfig->getFrequencyEndValue(),
            $recurringConfig->getFrequencyEndType()
        );
    }

    private function shiftStartDateForComparison(RecurringConfig $recurringConfig): Carbon
    {
        return match ($recurringConfig->getFrequencyType()) {
            FrequencyTypeEnum::DAY => $recurringConfig->getStartDate()->copy()->subDays($recurringConfig->getFrequencyInterval()),
            FrequencyTypeEnum::WEEK => $recurringConfig->getStartDate()->copy()->subWeeks($recurringConfig->getFrequencyInterval()),
            FrequencyTypeEnum::MONTH => $recurringConfig->getStartDate()->copy()->subMonths($recurringConfig->getFrequencyInterval()),
            FrequencyTypeEnum::YEAR => $recurringConfig->getStartDate()->copy()->subYears($recurringConfig->getFrequencyInterval()),
        };
    }

    /**
     * @return WeekdayEnum[]|array{day:int,month:int}|int|null
     */
    private function normalizeRepeatIn(RecurringConfig $recurringConfig): array|int|null
    {
        $repeatIn = $recurringConfig->getRepeatIn();

        return match ($recurringConfig->getFrequencyType()) {
            FrequencyTypeEnum::DAY => null,

            FrequencyTypeEnum::WEEK => array_map(
                fn ($weekday): WeekdayEnum => $weekday instanceof WeekdayEnum
                    ? $weekday
                    : WeekdayEnum::from($weekday),
                $repeatIn
            ),

            FrequencyTypeEnum::MONTH => (int) $repeatIn,

            FrequencyTypeEnum::YEAR => [
                'day' => (int) $repeatIn['day'],
                'month' => (int) $repeatIn['month'],
            ],
        };
    }
}
