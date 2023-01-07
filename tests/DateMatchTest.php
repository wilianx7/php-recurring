<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\RecurringConfig;
use Tightenco\Collect\Support\Collection;

class DateMatchTest extends AbstractTestCase
{
    public function test_every_day_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 12, 26))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER);

        $currentDate = Carbon::createFromDate(2019, 12, 26)->addDay();
        $datesCollection = new Collection();

        self::assertTrue($this->dateMatch($config, $currentDate, $datesCollection));
    }

    public function test_three_days_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 12, 26))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER);

        $currentDate = Carbon::createFromDate(2019, 12, 26)->addDay();
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate, $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addDays(2), $datesCollection));
    }

    public function test_every_week_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 12, 2))
            ->setFrequencyType(FrequencyTypeEnum::WEEK)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setRepeatIn([WeekdayEnum::MONDAY, WeekdayEnum::FRIDAY]);

        $currentDate = Carbon::createFromDate(2019, 12, 2);
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate, $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addWeeks(1), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addDays(4), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addWeeks(1), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addDays(4), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addDays(4), $datesCollection));
    }

    public function test_three_Weeks_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 12, 2))
            ->setFrequencyType(FrequencyTypeEnum::WEEK)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setRepeatIn([WeekdayEnum::MONDAY, WeekdayEnum::FRIDAY]);

        $currentDate = Carbon::createFromDate(2019, 12, 2);
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate, $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addWeeks(3), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addDays(4), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addDay(), $datesCollection));
    }

    public function test_every_month_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 8, 2))
            ->setFrequencyType(FrequencyTypeEnum::MONTH)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setRepeatIn(10);

        $currentDate = Carbon::createFromDate(2019, 8, 2);
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addDays(8), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addMonth()->addDay(), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
    }

    public function test_five_month_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 1, 10))
            ->setFrequencyType(FrequencyTypeEnum::MONTH)
            ->setFrequencyInterval(5)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setRepeatIn(10);

        $currentDate = Carbon::createFromDate(2019, 1, 10);
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addMonths(4), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addMonths(5), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
    }

    public function test_every_year_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 8, 2))
            ->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setRepeatIn(['day' => 2, 'month' => 9]);

        $currentDate = Carbon::createFromDate(2019, 8, 2);
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addYear(), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addYear(), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addDay()->addYear(), $datesCollection));
    }

    public function test_two_year_recurrence_never_end(): void
    {
        $config = new RecurringConfig();

        $config->setStartDate(Carbon::createFromDate(2019, 8, 2))
            ->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setFrequencyInterval(2)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setRepeatIn(['day' => 2, 'month' => 9]);

        $currentDate = Carbon::createFromDate(2019, 8, 2);
        $datesCollection = new Collection();

        self::assertFalse($this->dateMatch($config, $currentDate->addMonth(), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addYears(2), $datesCollection));
        self::assertTrue($this->dateMatch($config, $currentDate->addYears(2), $datesCollection));
        self::assertFalse($this->dateMatch($config, $currentDate->addDay()->addYears(2), $datesCollection));
    }
}