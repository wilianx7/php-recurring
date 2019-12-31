<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\Exceptions\InvalidExceptDate;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringConfig;
use Tightenco\Collect\Support\Collection;

class GenerateDatesTest extends AbstractTestCase
{
    /** @test */
    public function every_day_recurrence_never_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = $this->generateDates($config);

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[4]);
    }

    /** @test
     * @throws InvalidExceptDate
     */
    public function every_day_recurrence_never_end_with_except_dates()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setExceptDates(new Collection([Carbon::create(2019, 12, 28, 8, 0, 0), Carbon::create(2019, 12, 30)]));

        $datesCollection = $this->generateDates($config);

        self::assertCount(3, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[2]);
    }

    /** @test */
    public function every_day_recurrence_never_end_with_last_repeated_date()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = $this->generateDates($config);

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[4]);

        $config->setLastRepeatedDate($datesCollection->last());
        $this->generateDates($config);
        $datesCollection = $this->generateDates($config);

        self::assertCount(0, $datesCollection);

        $config->setStartDate(Carbon::create(2020, 12, 26, 8, 0, 0));
        $config->setEndDate($config->getStartDate()->copy()->endOfYear());

        $datesCollection = $this->generateDates($config);

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2020, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2020, 12, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2020, 12, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2020, 12, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2020, 12, 31, 8, 0, 0), $datesCollection[4]);

        $config->setLastRepeatedDate($datesCollection->last());
        $datesCollection = $this->generateDates($config);

        self::assertCount(0, $datesCollection);
    }

    /** @test */
    public function three_days_recurrence_after_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 1, 4, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 7, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 1, 10, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 1, 13, 8, 0, 0), $datesCollection[3]);
    }

    /** @test */
    public function three_days_recurrence_after_end_with_repeated_count()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 25, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4);

        $datesCollection = $this->generateDates($config);

        self::assertCount(2, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 28, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[1]);

        $config->setRepeatedCount($datesCollection->count());
        $config->setLastRepeatedDate($datesCollection->last());
        $config->setEndDate(Carbon::create(2020, 12, 31));

        $datesCollection = $this->generateDates($config);

        self::assertCount(2, $datesCollection);
        self::assertEquals(Carbon::create(2020, 1, 3, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2020, 1, 6, 8, 0, 0), $datesCollection[1]);
    }

    /** @test */
    public function every_week_recurrence_after_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::WEEK())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4)
            ->setRepeatIn([WeekdayEnum::MONDAY(), WeekdayEnum::SUNDAY()]);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 1, 7, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 13, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 1, 14, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 1, 20, 8, 0, 0), $datesCollection[3]);
    }

    /** @test */
    public function three_weeks_recurrence_after_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::WEEK())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4)
            ->setRepeatIn([WeekdayEnum::MONDAY(), WeekdayEnum::SUNDAY()]);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 1, 21, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 27, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 2, 11, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 2, 17, 8, 0, 0), $datesCollection[3]);
    }

    /** @test */
    public function every_month_recurrence_in_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::MONTH())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::IN())
            ->setFrequencyEndValue(Carbon::create(2019, 5, 1))
            ->setRepeatIn(31);

        $datesCollection = $this->generateDates($config);

        self::assertCount(3, $datesCollection);
        self::assertEquals(Carbon::create(2019, 2, 28, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 3, 31, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 4, 30, 8, 0, 0), $datesCollection[2]);
    }

    /** @test */
    public function three_months_recurrence_in_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::MONTH())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::IN())
            ->setFrequencyEndValue(Carbon::create(2019, 10, 1))
            ->setRepeatIn(31);

        $datesCollection = $this->generateDates($config);

        self::assertCount(2, $datesCollection);
        self::assertEquals(Carbon::create(2019, 4, 30, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 7, 31, 8, 0, 0), $datesCollection[1]);
    }

    /** @test */
    public function every_year_recurrence_after_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::YEAR())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2023, 12, 31, 8, 0, 0));

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2020, 2, 29, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2021, 2, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2022, 2, 28, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2023, 2, 28, 8, 0, 0), $datesCollection[3]);
    }

    /** @test */
    public function three_years_recurrence_after_end()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::YEAR())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2031, 12, 31, 8, 0, 0));

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2022, 2, 28, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2025, 2, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2028, 2, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2031, 2, 28, 8, 0, 0), $datesCollection[3]);
    }

    /** @test */
    public function three_years_recurrence_after_end_with_repeated_count()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::YEAR())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2031, 12, 31, 8, 0, 0));

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2022, 2, 28, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2025, 2, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2028, 2, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2031, 2, 28, 8, 0, 0), $datesCollection[3]);

        $config->setRepeatedCount(4);
        $datesCollection = $this->generateDates($config);

        self::assertCount(0, $datesCollection);
    }

    /** @test */
    public function invalid_frequency_end_value()
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue('invalid')
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $this->expectException(InvalidFrequencyEndValue::class);

        $this->generateDates($config);
    }
}