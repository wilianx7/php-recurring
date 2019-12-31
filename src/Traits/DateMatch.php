<?php


namespace PhpRecurring\Traits;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\RecurringConfig;
use Tightenco\Collect\Support\Collection;

trait DateMatch
{
    protected function dateMatch(RecurringConfig $recurringConfig, Carbon $currentDate, Collection $datesCollection): bool
    {
        if ($this->shouldGenerate($recurringConfig, $currentDate->copy(), $datesCollection)) {
            switch ($recurringConfig->getFrequencyType()) {
                case FrequencyTypeEnum::DAY():
                    return $this->dateMatchForDay($recurringConfig, $currentDate);
                case FrequencyTypeEnum::WEEK():
                    return $this->dateMatchForWeek($recurringConfig, $currentDate);
                case FrequencyTypeEnum::MONTH():
                    return $this->dateMatchForMonth($recurringConfig, $currentDate);
                case FrequencyTypeEnum::YEAR():
                    return $this->dateMatchForYear($recurringConfig, $currentDate);
            }
        }

        return false;
    }

    private function dateMatchForDay(RecurringConfig $recurringConfig, Carbon $currentDate)
    {
        $repeatInterval = $recurringConfig->getFrequencyInterval();
        $diffInDays = $currentDate->diffInDays($recurringConfig->getStartDate());

        if (($diffInDays != 0 && ($diffInDays % $repeatInterval == 0))) {
            return true;
        }

        return false;
    }

    private function dateMatchForWeek(RecurringConfig $recurringConfig, Carbon $currentDate)
    {
        $currentWeekday = WeekdayEnum::make(strtoupper($currentDate->englishDayOfWeek));
        $repeatIn = $recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();
        $diffInWeeks = $currentDate->diffInWeeks($recurringConfig->getStartDate()->startOfWeek());

        if (($diffInWeeks != 0 && ($diffInWeeks % $repeatInterval == 0)) && (in_array($currentWeekday, $repeatIn))) {
            return true;
        }

        return false;
    }

    private function dateMatchForMonth(RecurringConfig $recurringConfig, Carbon $currentDate)
    {
        $currentDay = $currentDate->day;
        $lastDayOfMonth = $currentDate->daysInMonth;
        $diffInMonths = $currentDate->diffInMonths($recurringConfig->getStartDate()->startOfMonth());
        $repeatIn = $recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        if (($diffInMonths != 0 && ($diffInMonths % $repeatInterval == 0)) &&
            ($currentDay == $repeatIn || $currentDay == $lastDayOfMonth && $repeatIn > $lastDayOfMonth)) {
            return true;
        }

        return false;
    }

    private function dateMatchForYear(RecurringConfig $recurringConfig, Carbon $currentDate)
    {
        $currentDay = $currentDate->day;
        $currentMonth = $currentDate->month;
        $lastDayOfMonth = $currentDate->daysInMonth;
        $diffInYears = $currentDate->diffInYears($recurringConfig->getStartDate()->startOfYear());
        $repeatIn = (object)$recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        if (($diffInYears != 0 && ($diffInYears % $repeatInterval == 0))
            && (($currentDay == $repeatIn->day || $currentDay == $lastDayOfMonth && (int)$repeatIn->day > $lastDayOfMonth)
                && $currentMonth == $repeatIn->month)) {
            return true;
        }

        return false;
    }
}