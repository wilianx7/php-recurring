<?php


namespace PhpRecurring\Traits;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\RecurringConfig;
use Illuminate\Support\Collection;

trait DateMatch
{
    protected function dateMatch(
        RecurringConfig $recurringConfig,
        Carbon $currentDate,
        Collection $datesCollection
    ): bool {
        if ($this->shouldGenerate($recurringConfig, $currentDate->copy(), $datesCollection)) {
            return match ($recurringConfig->getFrequencyType()) {
                FrequencyTypeEnum::DAY => $this->dateMatchForDay($recurringConfig, $currentDate),
                FrequencyTypeEnum::WEEK => $this->dateMatchForWeek($recurringConfig, $currentDate),
                FrequencyTypeEnum::MONTH => $this->dateMatchForMonth($recurringConfig, $currentDate),
                FrequencyTypeEnum::YEAR => $this->dateMatchForYear($recurringConfig, $currentDate)
            };
        }

        return false;
    }

    private function dateMatchForDay(RecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $repeatInterval = $recurringConfig->getFrequencyInterval();
        $diffInDays = (int)$currentDate->diffInDays($recurringConfig->getStartDate());

        return $diffInDays != 0 && ($diffInDays % $repeatInterval == 0);
    }

    private function dateMatchForWeek(RecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $currentWeekday = WeekdayEnum::from(strtoupper($currentDate->englishDayOfWeek));
        $repeatIn = $recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();
        $diffInWeeks = (int)$currentDate->diffInWeeks($recurringConfig->getStartDate()->startOfWeek(), true);

        return ($diffInWeeks != 0 && ($diffInWeeks % $repeatInterval == 0)) && (in_array($currentWeekday, $repeatIn));
    }

    private function dateMatchForMonth(RecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $currentDay = $currentDate->day;
        $lastDayOfMonth = $currentDate->daysInMonth;
        $diffInMonths = (int)$currentDate->diffInMonths($recurringConfig->getStartDate()->startOfMonth());
        $repeatIn = $recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        return $diffInMonths != 0
            && ($diffInMonths % $repeatInterval == 0)
            && $this->isValidDayOfMonth($currentDay, $repeatIn, $lastDayOfMonth);
    }

    private function dateMatchForYear(RecurringConfig $recurringConfig, Carbon $currentDate): bool
    {
        $currentDay = $currentDate->day;
        $currentMonth = $currentDate->month;
        $lastDayOfMonth = $currentDate->daysInMonth;
        $diffInYears = (int)$currentDate->diffInYears($recurringConfig->getStartDate()->startOfYear());
        $repeatIn = (object)$recurringConfig->getRepeatIn();
        $repeatInterval = $recurringConfig->getFrequencyInterval();

        return $diffInYears != 0 && ($diffInYears % $repeatInterval == 0)
            && $currentMonth == $repeatIn->month
            && $this->isValidDayOfMonth($currentDay, (int)$repeatIn->day, $lastDayOfMonth);
    }

    private function isValidDayOfMonth(int $currentDay, int $targetDay, int $lastDayOfMonth): bool
    {
        return $currentDay == $targetDay || ($currentDay == $lastDayOfMonth && $targetDay > $lastDayOfMonth);
    }
}