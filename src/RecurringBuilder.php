<?php


namespace PhpRecurring;

use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\Exceptions\InvalidFrequencyInterval;
use PhpRecurring\Exceptions\InvalidRepeatedCount;
use PhpRecurring\Exceptions\InvalidRepeatIn;
use PhpRecurring\Traits\DateMatch;
use PhpRecurring\Traits\GenerateDates;
use PhpRecurring\Traits\GenerateEndDate;
use PhpRecurring\Traits\ShouldGenerate;
use Illuminate\Support\Collection;

class RecurringBuilder
{
    use DateMatch,
        GenerateDates,
        GenerateEndDate,
        ShouldGenerate;

    public function __construct(private RecurringConfig $recurringConfig)
    {
    }

    public static function forConfig(RecurringConfig $recurringConfig): self
    {
        return new self($recurringConfig);
    }

    /**
     * @throws InvalidFrequencyInterval
     * @throws InvalidFrequencyEndValue
     * @throws InvalidRepeatIn
     * @throws InvalidRepeatedCount
     */
    public function startRecurring(): Collection
    {
        if ($this->recurringConfig->isValid()) {
            $this->recurringConfig->bindWeekdays();

            return $this->generateDates($this->recurringConfig);
        }

        return new Collection();
    }
}