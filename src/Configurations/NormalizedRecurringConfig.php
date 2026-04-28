<?php

namespace PhpRecurring\Configurations;

use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;

class NormalizedRecurringConfig
{
    /**
     * @param WeekdayEnum[]|array{day:int,month:int}|int|null $repeatIn
     * @param Carbon[] $exceptDates
     */
    public function __construct(
        private Carbon $originalStartDate,
        private Carbon $comparisonStartDate,
        private ?Carbon $endDate,
        private FrequencyTypeEnum $frequencyType,
        private int $frequencyInterval,
        private array|int|null $repeatIn,
        private FrequencyEndTypeEnum $frequencyEndType,
        private ?Carbon $frequencyEndDate,
        private ?int $occurrenceLimit,
        private ?Carbon $lastRepeatedDate,
        private int $repeatedCount,
        private array $exceptDates,
        private bool $includeStartDate
    ) {
    }

    public function getOriginalStartDate(): Carbon
    {
        return $this->originalStartDate;
    }

    public function getComparisonStartDate(): Carbon
    {
        return $this->comparisonStartDate;
    }

    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    public function getFrequencyType(): FrequencyTypeEnum
    {
        return $this->frequencyType;
    }

    public function getFrequencyInterval(): int
    {
        return $this->frequencyInterval;
    }

    /**
     * @return WeekdayEnum[]|array{day:int,month:int}|int|null
     */
    public function getRepeatIn(): array|int|null
    {
        return $this->repeatIn;
    }

    public function getFrequencyEndType(): FrequencyEndTypeEnum
    {
        return $this->frequencyEndType;
    }

    public function getFrequencyEndDate(): ?Carbon
    {
        return $this->frequencyEndDate;
    }

    public function getOccurrenceLimit(): ?int
    {
        return $this->occurrenceLimit;
    }

    public function getLastRepeatedDate(): ?Carbon
    {
        return $this->lastRepeatedDate;
    }

    public function getRepeatedCount(): int
    {
        return $this->repeatedCount;
    }

    /**
     * @return Carbon[]
     */
    public function getExceptDates(): array
    {
        return $this->exceptDates;
    }

    public function shouldIncludeStartDate(): bool
    {
        return $this->includeStartDate;
    }
}
