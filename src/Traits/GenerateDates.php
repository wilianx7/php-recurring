<?php

namespace PhpRecurring\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringConfig;

trait GenerateDates
{
    private RecurringConfig $recurringConfig;
    private ?Carbon $endDate;
    private Collection $datesCollection;

    /**
     * @throws InvalidFrequencyEndValue
     */
    protected function generateDates(RecurringConfig $recurringConfig): Collection
    {
        $this->recurringConfig = $recurringConfig;
        $this->datesCollection = new Collection();
        $this->bindEndDate();
        $frequencyEndValue = $this->getFrequencyEndValue();

        if ($this->recurringConfig->getIncludeStartDate()) {
            if (!$this->isStartDateExcepted()) {
                $this->datesCollection->push($recurringConfig->getStartDate()->copy());
            }

            $this->bindStartDate();
        }

        $currentDate = $recurringConfig->getStartDate()->copy();

        while ($this->getWhileCondition($currentDate, $frequencyEndValue)) {
            if ($recurringConfig->getExceptDates()?->contains($currentDate->copy()->setTime(0, 0))) {
                continue;
            }

            if (
                $recurringConfig->getIncludeStartDate() && $this->datesCollection->isNotEmpty()
                && $currentDate->copy()->lte($this->datesCollection->first())
            ) {
                continue;
            }

            if ($this->dateMatch($this->recurringConfig, $currentDate->copy(), $this->datesCollection)) {
                $this->datesCollection->push($currentDate->copy());
            }
        }

        return $this->datesCollection;
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
            $frequencyEndValue = (int)$this->recurringConfig->getFrequencyEndValue();

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
                && ($this->datesCollection->count() + $this->recurringConfig->getRepeatedCount()) < $frequencyEndValue;
        }

        return (!$this->endDate || $currentDate->lte($this->endDate))
            && $this->datesCollection->count() < $frequencyEndValue;
    }

    private function isStartDateExcepted(): bool
    {
        return (bool)$this->recurringConfig->getExceptDates()?->contains(
            $this->recurringConfig->getStartDate()->copy()->setTime(0, 0)
        );
    }
}