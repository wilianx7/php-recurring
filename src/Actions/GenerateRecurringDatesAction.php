<?php

namespace PhpRecurring\Actions;

use Carbon\Carbon;
use PhpRecurring\Configurations\NormalizedRecurringConfig;

class GenerateRecurringDatesAction
{
    public function __construct(
        private MatchRecurringDateAction $matchRecurringDateAction = new MatchRecurringDateAction(),
        private ShouldGenerateDateAction $shouldGenerateDateAction = new ShouldGenerateDateAction()
    ) {
    }

    /**
     * @return Carbon[]
     */
    public function execute(NormalizedRecurringConfig $recurringConfig): array
    {
        $generatedDates = [];

        if ($this->shouldIncludeStartDate($recurringConfig)) {
            $generatedDates[] = $recurringConfig->getOriginalStartDate()->copy();
        }

        $currentDate = $recurringConfig->getComparisonStartDate()->copy();

        while (true) {
            $currentDate->addDay();

            if (!$this->canContinue($recurringConfig, $currentDate, $generatedDates)) {
                break;
            }

            if ($this->isDateExcepted($recurringConfig, $currentDate)) {
                continue;
            }

            if (
                $recurringConfig->shouldIncludeStartDate()
                && $currentDate->lte($recurringConfig->getOriginalStartDate())
            ) {
                continue;
            }

            if ($this->matchRecurringDateAction->execute($recurringConfig, $currentDate->copy(), $generatedDates)) {
                $generatedDates[] = $currentDate->copy();
            }
        }

        return $generatedDates;
    }

    /**
     * @param Carbon[] $generatedDates
     */
    private function canContinue(
        NormalizedRecurringConfig $recurringConfig,
        Carbon $currentDate,
        array $generatedDates
    ): bool {
        $endDate = $recurringConfig->getEndDate();

        if ($endDate && $currentDate->gt($endDate)) {
            return false;
        }

        $occurrenceLimit = $recurringConfig->getOccurrenceLimit();

        if ($occurrenceLimit !== null) {
            return ($recurringConfig->getRepeatedCount() + count($generatedDates)) < $occurrenceLimit;
        }

        return true;
    }

    private function isDateExcepted(NormalizedRecurringConfig $recurringConfig, Carbon $date): bool
    {
        $normalizedDate = $date->copy()->setTime(0, 0);

        foreach ($recurringConfig->getExceptDates() as $exceptDate) {
            if ($exceptDate->equalTo($normalizedDate)) {
                return true;
            }
        }

        return false;
    }

    private function shouldIncludeStartDate(NormalizedRecurringConfig $recurringConfig): bool
    {
        return $recurringConfig->shouldIncludeStartDate()
            && !$this->isDateExcepted($recurringConfig, $recurringConfig->getOriginalStartDate())
            && $this->shouldGenerateDateAction->execute(
                $recurringConfig,
                $recurringConfig->getOriginalStartDate()->copy(),
                []
            );
    }
}
