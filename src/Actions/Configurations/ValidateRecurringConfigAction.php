<?php

namespace PhpRecurring\Actions\Configurations;

use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\Exceptions\InvalidFrequencyInterval;
use PhpRecurring\Exceptions\InvalidRepeatedCount;
use PhpRecurring\Exceptions\InvalidRepeatIn;
use PhpRecurring\RecurringConfig;
use ValueError;

class ValidateRecurringConfigAction
{
    /**
     * @throws InvalidFrequencyEndValue
     * @throws InvalidFrequencyInterval
     * @throws InvalidRepeatedCount
     * @throws InvalidRepeatIn
     */
    public function execute(RecurringConfig $recurringConfig): bool
    {
        if ($recurringConfig->getFrequencyInterval() <= 0) {
            throw new InvalidFrequencyInterval();
        }

        if ($this->isInvalidFrequencyEndValue($recurringConfig)) {
            throw new InvalidFrequencyEndValue();
        }

        if (($recurringConfig->getRepeatedCount() ?? 0) < 0) {
            throw new InvalidRepeatedCount();
        }

        $this->validateRepeatIn($recurringConfig);

        return true;
    }

    /**
     * @throws InvalidRepeatIn
     */
    private function validateRepeatIn(RecurringConfig $recurringConfig): void
    {
        $repeatIn = $recurringConfig->getRepeatIn();

        match ($recurringConfig->getFrequencyType()) {
            FrequencyTypeEnum::DAY => null,
            FrequencyTypeEnum::WEEK => $this->validateWeeklyRepeatIn($repeatIn),
            FrequencyTypeEnum::MONTH => $this->validateMonthlyRepeatIn($repeatIn),
            FrequencyTypeEnum::YEAR => $this->validateYearlyRepeatIn($repeatIn),
        };
    }

    private function isInvalidFrequencyEndValue(RecurringConfig $recurringConfig): bool
    {
        $frequencyEndType = $recurringConfig->getFrequencyEndType();
        $frequencyEndValue = $recurringConfig->getFrequencyEndValue();

        return ($frequencyEndType !== FrequencyEndTypeEnum::NEVER && !$frequencyEndValue)
            || ($frequencyEndType === FrequencyEndTypeEnum::IN && !($frequencyEndValue instanceof Carbon))
            || ($frequencyEndType === FrequencyEndTypeEnum::AFTER && !is_int($frequencyEndValue));
    }

    /**
     * @throws InvalidRepeatIn
     */
    private function validateWeeklyRepeatIn(string|array|null $repeatIn): void
    {
        if (!is_array($repeatIn) || $repeatIn === []) {
            throw new InvalidRepeatIn();
        }

        foreach ($repeatIn as $weekday) {
            try {
                if (!$weekday instanceof WeekdayEnum) {
                    WeekdayEnum::from($weekday);
                }
            } catch (ValueError) {
                throw new InvalidRepeatIn();
            }
        }
    }

    /**
     * @throws InvalidRepeatIn
     */
    private function validateMonthlyRepeatIn(string|array|null $repeatIn): void
    {
        if (
            (!is_int($repeatIn) && !(is_string($repeatIn) && ctype_digit($repeatIn)))
            || (int) $repeatIn <= 0
            || (int) $repeatIn > 31
        ) {
            throw new InvalidRepeatIn();
        }
    }

    /**
     * @throws InvalidRepeatIn
     */
    private function validateYearlyRepeatIn(string|array|null $repeatIn): void
    {
        if (!is_array($repeatIn)) {
            throw new InvalidRepeatIn();
        }

        $day = $repeatIn['day'] ?? null;
        $month = $repeatIn['month'] ?? null;

        if (
            (!$this->isPositiveNumeric($day))
            || (!$this->isPositiveNumeric($month))
            || (int) $day > 31
            || (int) $month > 12
        ) {
            throw new InvalidRepeatIn();
        }
    }

    private function isPositiveNumeric(mixed $value): bool
    {
        return is_int($value) || (is_string($value) && ctype_digit($value) && (int) $value > 0);
    }
}
