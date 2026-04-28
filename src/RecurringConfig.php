<?php

namespace PhpRecurring;

use PhpRecurring\Actions\Configurations\ValidateRecurringConfigAction;
use Carbon\Carbon;
use DateTimeInterface;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\Exceptions\InvalidExceptDate;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\Exceptions\InvalidFrequencyInterval;
use PhpRecurring\Exceptions\InvalidRepeatedCount;
use PhpRecurring\Exceptions\InvalidRepeatIn;

class RecurringConfig
{
    private Carbon $startDate;
    private ?Carbon $endDate;
    private FrequencyTypeEnum $frequencyType;
    private int $frequencyInterval;
    private string|array|null $repeatIn;
    private FrequencyEndTypeEnum $frequencyEndType;
    private Carbon|int|null $frequencyEndValue;
    private ?Carbon $lastRepeatedDate;
    private ?int $repeatedCount;
    private ?array $exceptDates;
    private bool $includeStartDate;

    public function __construct(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null,
        FrequencyTypeEnum $frequencyType = FrequencyTypeEnum::DAY,
        int $frequencyInterval = 1,
        string|array|null $repeatIn = null,
        FrequencyEndTypeEnum $frequencyEndType = FrequencyEndTypeEnum::NEVER,
        DateTimeInterface|int|null $frequencyEndValue = null,
        ?DateTimeInterface $lastRepeatedDate = null,
        ?int $repeatedCount = null,
        ?array $exceptDates = null,
        bool $includeStartDate = false
    ) {
        $this->startDate = $startDate ? $this->toCarbon($startDate) : Carbon::now()->startOfYear();
        $this->endDate = $endDate ? $this->toCarbon($endDate) : null;
        $this->frequencyType = $frequencyType;
        $this->frequencyInterval = $frequencyInterval;
        $this->repeatIn = $repeatIn;
        $this->frequencyEndType = $frequencyEndType;
        $this->lastRepeatedDate = $lastRepeatedDate ? $this->toCarbon($lastRepeatedDate) : null;
        $this->repeatedCount = $repeatedCount;
        $this->exceptDates = null;
        $this->includeStartDate = $includeStartDate;

        $this->frequencyEndValue = $frequencyEndValue instanceof DateTimeInterface
            ? $this->toCarbon($frequencyEndValue)
            : $frequencyEndValue;

        if ($exceptDates) {
            $this->setExceptDates($exceptDates);
        }
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /** Date when the recurrence will start. */
    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $this->toCarbon($startDate);

        return $this;
    }

    public function getIncludeStartDate(): ?bool
    {
        return $this->includeStartDate;
    }

    /** When true, the start date will be included in result of recurring dates. */
    public function setIncludeStartDate(?bool $includeStartDate): self
    {
        $this->includeStartDate = $includeStartDate;

        return $this;
    }

    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /** End date for recurrence generation. If null, the end date will be assumed as the end of the current year. */
    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate ? $this->toCarbon($endDate) : null;

        return $this;
    }

    public function getFrequencyType(): FrequencyTypeEnum
    {
        return $this->frequencyType;
    }

    /** How often the recurrence will be generated. DAY | WEEK | MONTH | YEAR. */
    public function setFrequencyType(FrequencyTypeEnum $frequencyType): self
    {
        $this->frequencyType = $frequencyType;

        return $this;
    }

    public function getFrequencyInterval(): int
    {
        return $this->frequencyInterval;
    }

    /** Determines the interval between recurrences according to the chosen frequency. */
    public function setFrequencyInterval(int $frequencyInterval): self
    {
        $this->frequencyInterval = $frequencyInterval;

        return $this;
    }

    public function getRepeatIn(): string|array|null
    {
        return $this->repeatIn;
    }

    /** Determines when recurrence should be generated according to the frequency chosen. */
    public function setRepeatIn(string|array|null $repeatIn): self
    {
        $this->repeatIn = $repeatIn;

        return $this;
    }

    public function getFrequencyEndType(): FrequencyEndTypeEnum
    {
        return $this->frequencyEndType;
    }

    /** Determines what will be the stopping criterion for recurrence generation. NEVER | IN | AFTER. */
    public function setFrequencyEndType(FrequencyEndTypeEnum $frequencyEndType): self
    {
        $this->frequencyEndType = $frequencyEndType;

        return $this;
    }

    public function getFrequencyEndValue(): Carbon|int|null
    {
        return $this->frequencyEndValue;
    }

    /** Determines a value according to the chosen stop criterion. */
    public function setFrequencyEndValue(DateTimeInterface|int|null $frequencyEndValue): self
    {
        $this->frequencyEndValue = $frequencyEndValue instanceof DateTimeInterface
            ? $this->toCarbon($frequencyEndValue)
            : $frequencyEndValue;

        return $this;
    }

    public function getLastRepeatedDate(): ?Carbon
    {
        return $this->lastRepeatedDate;
    }

    /** Date the last recurrence was generated. */
    public function setLastRepeatedDate(?DateTimeInterface $lastRepeatedDate): self
    {
        $this->lastRepeatedDate = $lastRepeatedDate ? $this->toCarbon($lastRepeatedDate) : null;

        return $this;
    }

    public function getRepeatedCount(): ?int
    {
        return $this->repeatedCount;
    }

    /** How many recurrences have already been generated. */
    public function setRepeatedCount(?int $repeatedCount): self
    {
        $this->repeatedCount = $repeatedCount;

        return $this;
    }

    public function getExceptDates(): ?array
    {
        return $this->exceptDates;
    }

    /** Dates when recurrence should not be generated.
     *
     * @throws InvalidExceptDate
     */
    public function setExceptDates(?array $exceptDates): self
    {
        if ($exceptDates) {
            $this->exceptDates = [];

            foreach ($exceptDates as $exceptDate) {
                if ($exceptDate instanceof DateTimeInterface) {
                    $this->exceptDates[] = $this->toCarbon($exceptDate)->setTime(0, 0);
                } elseif (Carbon::hasFormat($exceptDate, 'Y-m-d H:i:s')) {
                    $this->exceptDates[] = Carbon::createFromFormat('Y-m-d H:i:s', $exceptDate)->setTime(0, 0);
                } else {
                    throw new InvalidExceptDate();
                }
            }
        }

        return $this;
    }

    /** Ensure repeatIn is instance of WeekdayEnum  */
    public function bindWeekdays(): void
    {
        if ($this->getFrequencyType() === FrequencyTypeEnum::WEEK) {
            $this->setRepeatIn(
                array_map(
                    fn ($weekday) => $weekday instanceof WeekdayEnum
                        ? $weekday
                        : WeekdayEnum::from($weekday),
                    $this->getRepeatIn()
                )
            );
        }
    }

    /**
     * Check if the settings are valid.
     *
     * @throws InvalidFrequencyEndValue
     * @throws InvalidFrequencyInterval
     * @throws InvalidRepeatedCount
     * @throws InvalidRepeatIn
     */
    public function isValid(): bool
    {
        return (new ValidateRecurringConfigAction())->execute($this);
    }

    private function toCarbon(DateTimeInterface $date): Carbon
    {
        return $date instanceof Carbon ? $date : Carbon::instance($date);
    }
}
