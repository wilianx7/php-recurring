<?php

namespace PhpRecurring\Traits;

use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringConfig;
use Tightenco\Collect\Support\Collection;

trait GenerateDates
{
    private RecurringConfig $recurringConfig;
    private ?Carbon $endDate;
    private Collection $datesCollection;

    protected function generateDates(RecurringConfig $recurringConfig): Collection
    {
        $this->recurringConfig = $recurringConfig;
        $this->datesCollection = new Collection();
        $currentDate = $recurringConfig->getStartDate()->copy();
        $this->bindEndDate();
        $frequencyEndValue = $this->getFrequencyEndValue();

        if ($this->shouldIncludeStartDate()) {
            $this->datesCollection->push($this->recurringConfig->getStartDate()->copy());
        }

        while ($this->getWhileCondition($currentDate, $frequencyEndValue)) {
            if ($recurringConfig->getExceptDates()
                && $recurringConfig->getExceptDates()->contains(
                    $currentDate->copy()->setTime(0, 0, 0, 0)
                )) {
                continue;
            }

            if ($this->dateMatch($this->recurringConfig, $currentDate->copy(), $this->datesCollection)) {
                $this->datesCollection->push($currentDate->copy());
            }
        }

        return $this->datesCollection;
    }

    private function bindEndDate()
    {
        if ($this->recurringConfig->getEndDate()) {
            $this->endDate = $this->recurringConfig->getEndDate();
        } else {
            $this->endDate = $this->generateEndDate($this->recurringConfig->getFrequencyEndValue(), $this->recurringConfig->getFrequencyEndType());
        }
    }

    private function getFrequencyEndValue()
    {
        if ($this->recurringConfig->getFrequencyEndType()->isEqual(FrequencyEndTypeEnum::AFTER())) {
            $frequencyEndValue = (int)$this->recurringConfig->getFrequencyEndValue();

            if ($frequencyEndValue != 0) {
                return $frequencyEndValue;
            }

            throw new InvalidFrequencyEndValue();
        }

        return $this->endDate;
    }

    private function shouldIncludeStartDate(): bool
    {
        if ($this->recurringConfig && $this->recurringConfig->getIncludeStartDate()) {
            if (!$this->recurringConfig->getExceptDates()) {
                return true;
            } else if (!$this->recurringConfig->getExceptDates()->contains(
                $this->recurringConfig->getStartDate()->copy()->setTime(0, 0, 0, 0)
            )) {
                return true;
            }
        }

        return false;
    }

    private function getWhileCondition(Carbon $currentDate, $frequencyEndValue)
    {
        $currentDate->addDay();

        if ($this->recurringConfig->getFrequencyEndType()->isEqual(FrequencyEndTypeEnum::NEVER())
            || $this->recurringConfig->getFrequencyEndType()->isEqual(FrequencyEndTypeEnum::IN())) {
            return ($currentDate->lte($this->endDate));
        } else if ($this->recurringConfig->getFrequencyEndType()->isEqual(FrequencyEndTypeEnum::AFTER())) {
            if ($this->recurringConfig->getRepeatedCount()) {
                return ($currentDate->lte($this->endDate)
                    && ((count($this->datesCollection) + $this->recurringConfig->getRepeatedCount()) < $frequencyEndValue));
            }

            return ($currentDate->lte($this->endDate) && (count($this->datesCollection) < $frequencyEndValue));
        }

        return false;
    }
}