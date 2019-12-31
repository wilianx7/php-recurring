<?php


namespace PhpRecurring;


use PhpRecurring\Traits\DateMatch;
use PhpRecurring\Traits\GenerateDates;
use PhpRecurring\Traits\GenerateEndDate;
use PhpRecurring\Traits\ShouldGenerate;
use Tightenco\Collect\Support\Collection;

class RecurringBuilder
{
    use DateMatch,
        GenerateDates,
        GenerateEndDate,
        ShouldGenerate;

    private RecurringConfig $recurringConfig;

    public function __construct(RecurringConfig $recurringConfig)
    {
        $this->recurringConfig = $recurringConfig;
    }

    public static function forConfig(RecurringConfig $recurringConfig): self
    {
        return new self($recurringConfig);
    }

    public function startRecurring(): Collection
    {
        if ($this->recurringConfig->isValid()) {
            return $this->generateDates($this->recurringConfig);
        }

        return new Collection();
    }
}