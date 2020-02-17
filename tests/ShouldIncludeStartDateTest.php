<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\Exceptions\InvalidExceptDate;
use PhpRecurring\RecurringConfig;
use Tightenco\Collect\Support\Collection;

class ShouldIncludeStartDateTest extends AbstractTestCase
{
    /** @test
     * @throws InvalidExceptDate
     */
    public function every_month_recurrence_never_end_with_except_dates_and_not_include_start_date()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 7, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::MONTH())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setExceptDates([Carbon::create(2019, 10, 26, 8, 0, 0), Carbon::create(2019, 8, 26)])
            ->setRepeatIn(26)
            ->setIncludeStartDate(false);

        $datesCollection = $this->generateDates($config);

        self::assertCount(3, $datesCollection);
        self::assertEquals(Carbon::create(2019, 9, 26, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 11, 26, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 26, 8, 0, 0), $datesCollection[2]);
    }

    /** @test
     * @throws InvalidExceptDate
     */
    public function every_month_recurrence_never_end_with_except_dates_and_include_start_date()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 7, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::MONTH())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setExceptDates([Carbon::create(2019, 10, 26, 8, 0, 0), Carbon::create(2019, 8, 26)])
            ->setRepeatIn(26)
            ->setIncludeStartDate(true);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 7, 26, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 9, 26, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 11, 26, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 26, 8, 0, 0), $datesCollection[3]);
    }

    /** @test
     * @throws InvalidExceptDate
     */
    public function every_day_recurrence_never_end_with_except_dates_and_include_start_date()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setExceptDates(new Collection([Carbon::create(2019, 12, 26, 8, 0, 0), Carbon::create(2019, 12, 28, 8, 0, 0), Carbon::create(2019, 12, 30)]))
            ->setIncludeStartDate(true);

        $datesCollection = $this->generateDates($config);

        self::assertCount(3, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[2]);
    }

    /** @test
     * @throws InvalidExceptDate
     */
    public function every_week_recurrence_after_end_with_include_start_date()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 10, 24, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::WEEK())
            ->setRepeatIn([WeekdayEnum::FRIDAY(), WeekdayEnum::MONDAY(), WeekdayEnum::WEDNESDAY(), WeekdayEnum::THURSDAY()])
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(5)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setIncludeStartDate(true);

        $datesCollection = $this->generateDates($config);
        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 10, 24, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 10, 25, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 10, 28, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 10, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 10, 31, 8, 0, 0), $datesCollection[4]);
    }
}