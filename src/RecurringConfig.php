<?php

namespace PhpRecurring;

use Carbon\Carbon;
use Exception;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Exceptions\InvalidExceptDate;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\Exceptions\InvalidFrequencyInterval;
use PhpRecurring\Exceptions\InvalidRepeatedCount;
use PhpRecurring\Exceptions\InvalidRepeatIn;
use Tightenco\Collect\Support\Collection;

class RecurringConfig
{
    private Carbon $startDate;
    private ?Carbon $endDate;
    private FrequencyTypeEnum $frequencyType;
    private int $frequencyInterval;
    private FrequencyEndTypeEnum $frequencyEndType;
    private ?Carbon $lastRepeatedDate;
    private ?int $repeatedCount;
    private ?Collection $exceptDates;
    private ?bool $includeStartDate;

    /**
     * @var $repeatIn string|array|null
     */
    private $repeatIn;

    /**
     * @var $frequencyEndValue Carbon|int|null
     */
    private $frequencyEndValue;

    public function __construct()
    {
        $this->startDate = Carbon::now()->startOfYear();
        $this->frequencyType = FrequencyTypeEnum::DAY();
        $this->frequencyInterval = 1;
        $this->frequencyEndType = FrequencyEndTypeEnum::NEVER();
        $this->endDate = null;
        $this->lastRepeatedDate = null;
        $this->repeatedCount = null;
        $this->exceptDates = null;
        $this->repeatIn = null;
        $this->frequencyEndValue = null;
        $this->includeStartDate = false;
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * Date when the recurrence will start.
     *
     * @param Carbon $startDate
     * @return RecurringConfig
     */
    public function setStartDate(Carbon $startDate): RecurringConfig
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIncludeStartDate(): ?bool
    {
        return $this->includeStartDate;
    }

    /**
     * @param bool|null $includeStartDate
     * @return RecurringConfig
     */
    public function setIncludeStartDate(?bool $includeStartDate): RecurringConfig
    {
        $this->includeStartDate = $includeStartDate;
        return $this;
    }

    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /**
     * End date for recurrence generation. If null, the end date will be assumed as the end of the current year.
     *
     * @param Carbon|null $endDate
     * @return RecurringConfig
     */
    public function setEndDate(?Carbon $endDate): RecurringConfig
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getFrequencyType(): FrequencyTypeEnum
    {
        return $this->frequencyType;
    }

    /**
     * How often the recurrence will be generated. DAY | WEEK | MONTH | YEAR.
     *
     * @param FrequencyTypeEnum $frequencyType
     * @return RecurringConfig
     */
    public function setFrequencyType(FrequencyTypeEnum $frequencyType): RecurringConfig
    {
        $this->frequencyType = $frequencyType;
        return $this;
    }

    public function getFrequencyInterval(): int
    {
        return $this->frequencyInterval;
    }

    /**
     * Determines the interval between recurrences according to the chosen frequency.
     *
     * @param int $frequencyInterval
     * @return RecurringConfig
     */
    public function setFrequencyInterval(int $frequencyInterval): RecurringConfig
    {
        $this->frequencyInterval = $frequencyInterval;
        return $this;
    }

    /**
     * @return string|array|null
     */
    public function getRepeatIn()
    {
        return $this->repeatIn;
    }

    /**
     * Determines when recurrence should be generated according to the frequency chosen.
     *
     * @param string|array|null $repeatIn
     * @return RecurringConfig
     */
    public function setRepeatIn($repeatIn)
    {
        $this->repeatIn = $repeatIn;
        return $this;
    }

    public function getFrequencyEndType(): FrequencyEndTypeEnum
    {
        return $this->frequencyEndType;
    }

    /**
     * Determines what will be the stopping criterion for recurrence generation. NEVER | IN | AFTER.
     *
     * @param FrequencyEndTypeEnum $frequencyEndType
     * @return RecurringConfig
     */
    public function setFrequencyEndType(FrequencyEndTypeEnum $frequencyEndType): RecurringConfig
    {
        $this->frequencyEndType = $frequencyEndType;
        return $this;
    }

    /**
     * @return Carbon|int|null
     */
    public function getFrequencyEndValue()
    {
        return $this->frequencyEndValue;
    }

    /**
     * Determines a value according to the chosen stop criterion.
     *
     * @param Carbon|int|null $frequencyEndValue
     * @return RecurringConfig
     */
    public function setFrequencyEndValue($frequencyEndValue): RecurringConfig
    {
        $this->frequencyEndValue = $frequencyEndValue;
        return $this;
    }

    public function getLastRepeatedDate(): ?Carbon
    {
        return $this->lastRepeatedDate;
    }

    /**
     * Date the last recurrence was generated.
     *
     * @param Carbon|null $lastRepeatedDate
     * @return RecurringConfig
     */
    public function setLastRepeatedDate(?Carbon $lastRepeatedDate): RecurringConfig
    {
        $this->lastRepeatedDate = $lastRepeatedDate;
        return $this;
    }

    public function getRepeatedCount(): ?int
    {
        return $this->repeatedCount;
    }

    /**
     * How many recurrences have already been generated.
     *
     * @param int|null $repeatedCount
     * @return RecurringConfig
     */
    public function setRepeatedCount(?int $repeatedCount): RecurringConfig
    {
        $this->repeatedCount = $repeatedCount;
        return $this;
    }

    public function getExceptDates(): ?Collection
    {
        return $this->exceptDates;
    }

    /**
     * Dates when recurrence should not be generated.
     *
     * @param array|Collection|null $exceptDates
     * @return RecurringConfig
     * @throws InvalidExceptDate
     */
    public function setExceptDates($exceptDates): RecurringConfig
    {
        if ($exceptDates) {
            $this->exceptDates = new Collection();
            foreach ($exceptDates as $exceptDate) {
                if ($exceptDate instanceof Carbon) {
                    $this->exceptDates->push($exceptDate->setTime(0, 0, 0, 0));
                } else {
                    throw new InvalidExceptDate();
                }
            }
        }

        return $this;
    }

    /**
     * Check if the settings are valid.
     *
     * @return bool
     * @throws InvalidFrequencyEndValue
     * @throws InvalidFrequencyInterval
     * @throws InvalidRepeatedCount
     * @throws InvalidRepeatIn
     */
    public function isValid()
    {
        if ($this->frequencyInterval <= 0) {
            throw new InvalidFrequencyInterval();
        }

        if (($this->frequencyEndType->isEqual(FrequencyEndTypeEnum::IN())
                || $this->frequencyEndType->isEqual(FrequencyEndTypeEnum::AFTER()))
            && !$this->frequencyEndValue) {
            throw new InvalidFrequencyEndValue();
        }

        if ($this->frequencyEndType->isEqual(FrequencyEndTypeEnum::IN())
            && !($this->frequencyEndValue instanceof Carbon)) {
            throw new InvalidFrequencyEndValue();
        }

        if ($this->frequencyEndType->isEqual(FrequencyEndTypeEnum::AFTER())
            && $this->frequencyEndValue instanceof Carbon) {
            throw new InvalidFrequencyEndValue();
        }

        if ($this->repeatedCount < 0) {
            throw new InvalidRepeatedCount();
        }

        if ($this->repeatIn && $this->frequencyType->isEqual(FrequencyTypeEnum::YEAR())) {
            try {
                $repeatIn = (object)$this->repeatIn;

                if (!isset($repeatIn->day) || !isset($repeatIn->month)) {
                    throw new InvalidRepeatIn();
                }
            } catch (Exception $exception) {
                throw new InvalidRepeatIn();
            }
        }

        return true;
    }
}