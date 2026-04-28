<?php

namespace PhpRecurring\Actions;

use Carbon\Carbon;
use PhpRecurring\Actions\Matchers\MatchDailyRecurringDateAction;
use PhpRecurring\Actions\Matchers\MatchMonthlyRecurringDateAction;
use PhpRecurring\Actions\Matchers\MatchWeeklyRecurringDateAction;
use PhpRecurring\Actions\Matchers\MatchYearlyRecurringDateAction;
use PhpRecurring\Configurations\NormalizedRecurringConfig;
use PhpRecurring\Enums\FrequencyTypeEnum;

class MatchRecurringDateAction
{
    public function __construct(
        private ShouldGenerateDateAction $shouldGenerateDateAction = new ShouldGenerateDateAction(),
        private MatchDailyRecurringDateAction $matchDailyRecurringDateAction = new MatchDailyRecurringDateAction(),
        private MatchWeeklyRecurringDateAction $matchWeeklyRecurringDateAction = new MatchWeeklyRecurringDateAction(),
        private MatchMonthlyRecurringDateAction $matchMonthlyRecurringDateAction = new MatchMonthlyRecurringDateAction(),
        private MatchYearlyRecurringDateAction $matchYearlyRecurringDateAction = new MatchYearlyRecurringDateAction()
    ) {
    }

    /**
     * @param Carbon[] $generatedDates
     */
    public function execute(
        NormalizedRecurringConfig $recurringConfig,
        Carbon $currentDate,
        array $generatedDates
    ): bool {
        if (!$this->shouldGenerateDateAction->execute($recurringConfig, $currentDate->copy(), $generatedDates)) {
            return false;
        }

        return match ($recurringConfig->getFrequencyType()) {
            FrequencyTypeEnum::DAY => $this->matchDailyRecurringDateAction->execute($recurringConfig, $currentDate),
            FrequencyTypeEnum::WEEK => $this->matchWeeklyRecurringDateAction->execute($recurringConfig, $currentDate),
            FrequencyTypeEnum::MONTH => $this->matchMonthlyRecurringDateAction->execute($recurringConfig, $currentDate),
            FrequencyTypeEnum::YEAR => $this->matchYearlyRecurringDateAction->execute($recurringConfig, $currentDate),
        };
    }
}
