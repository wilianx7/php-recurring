<?php

namespace PhpRecurring\Traits;

use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringConfig;

trait GenerateDates
{
    private RecurringConfig $recurringConfig;
    private ?Carbon $endDate;
    private array $generatedDates;

    /**
     * @throws InvalidFrequencyEndValue
     */
    protected function generateDates(RecurringConfig $recurringConfig): array
    {
        $this->recurringConfig = $recurringConfig;
        $this->generatedDates = [];
        $this->bindEndDate();
        $frequencyEndValue = $this->getFrequencyEndValue();

        if ($this->recurringConfig->getIncludeStartDate()) {
            if (!$this->isStartDateExcepted()) {
                $this->generatedDates[] = $recurringConfig->getStartDate()->copy();
            }

            $this->bindStartDate();
        }

        $currentDate = $recurringConfig->getStartDate()->copy();

        while ($this->getWhileCondition($currentDate, $frequencyEndValue)) {
            if ($this->isDateExcepted($currentDate)) {
                continue;
            }

            if (
                $recurringConfig->getIncludeStartDate() && !empty($this->generatedDates)
                && $currentDate->copy()->lte($this->generatedDates[0])
            ) {
                continue;
            }

            if ($this->dateMatch($this->recurringConfig, $currentDate->copy(), $this->generatedDates)) {
                $this->generatedDates[] = $currentDate->copy();
            }
        }

        return $this->generatedDates;
    }

    private function bindStartDate(): void
    {
        match ($this->recurringConfig->getFrequencyType()) {
            FrequencyTypeEnum::DAY => $this->recurringConfig->setStartDate(
                $this->recurringConfig->getStartDate()->copy()->subDays($this->recurringConfig->getFrequencyInterval())
            ),

            FrequencyTypeEnum::WEEK => $this->recurringConfig->setStartDate(
                $this->recurringConfig->getStartDate()->copy()->subWeeks($this->recurringConfig->getFrequencyInterval())
            ),

            FrequencyTypeEnum::MONTH => $this->recurringConfig->setStartDate(
                $this->recurringConfig->getStartDate()->copy()->subMonths($this->recurringConfig->getFrequencyInterval())
            ),

            FrequencyTypeEnum::YEAR => $this->recurringConfig->setStartDate(
                $this->recurringConfig->getStartDate()->copy()->subYears($this->recurringConfig->getFrequencyInterval())
            )
        };
    }

    private function bindEndDate(): void
    {
        $this->endDate = $this->recurringConfig->getEndDate()
            ?? $this->generateEndDate(
                $this->recurringConfig->getFrequencyEndValue(),
                $this->recurringConfig->getFrequencyEndType()
            );
    }

    /**
     * @throws InvalidFrequencyEndValue
     */
    private function getFrequencyEndValue(): int|Carbon
    {
        if ($this->recurringConfig->getFrequencyEndType() == FrequencyEndTypeEnum::AFTER) {
            $frequencyEndValue = (int) $this->recurringConfig->getFrequencyEndValue();

            if ($frequencyEndValue != 0) {
                return $frequencyEndValue;
            }

            throw new InvalidFrequencyEndValue();
        }

        return $this->endDate;
    }

    private function getWhileCondition(Carbon $currentDate, $frequencyEndValue): bool
    {
        $currentDate->addDay();

        if ($this->recurringConfig->getFrequencyEndType() != FrequencyEndTypeEnum::AFTER) {
            return $currentDate->lte($this->endDate);
        }

        if ($this->recurringConfig->getRepeatedCount()) {
            return (!$this->endDate || ($currentDate->lte($this->endDate)))
                && (count($this->generatedDates) + $this->recurringConfig->getRepeatedCount()) < $frequencyEndValue;
        }

        return (!$this->endDate || $currentDate->lte($this->endDate))
            && count($this->generatedDates) < $frequencyEndValue;
    }

    private function isStartDateExcepted(): bool
    {
        return $this->dateMatchesExceptDates($this->recurringConfig->getStartDate()->copy()->setTime(0, 0));
    }

    private function isDateExcepted(Carbon $currentDate): bool
    {
        return $this->dateMatchesExceptDates($currentDate->copy()->setTime(0, 0));
    }

    private function dateMatchesExceptDates(Carbon $date): bool
    {
        foreach ($this->recurringConfig->getExceptDates() ?? [] as $exceptDate) {
            if ($exceptDate->equalTo($date)) {
                return true;
            }
        }

        return false;
    }
}
