<?php

namespace PhpRecurring\Tests;

use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Enums\WeekdayEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringConfig;
use Illuminate\Support\Collection;

class GenerateDatesTest extends AbstractTestCase
{
    public function test_every_day_recurrence_never_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = $this->generateDates($config);

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[4]);
    }

    public function test_every_day_recurrence_never_end_with_except_dates(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setExceptDates(new Collection([Carbon::create(2019, 12, 28, 8), Carbon::create(2019, 12, 30)]));

        $datesCollection = $this->generateDates($config);

        self::assertCount(3, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[2]);
    }

    public function test_every_day_recurrence_never_end_with_last_repeated_date(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 26, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = $this->generateDates($config);

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[4]);

        $config->setLastRepeatedDate($datesCollection->last());
        $this->generateDates($config);
        $datesCollection = $this->generateDates($config);

        self::assertCount(0, $datesCollection);

        $config->setStartDate(Carbon::create(2020, 12, 26, 8));
        $config->setEndDate($config->getStartDate()->copy()->endOfYear());

        $datesCollection = $this->generateDates($config);

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2020, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2020, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2020, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2020, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2020, 12, 31, 8), $datesCollection[4]);

        $config->setLastRepeatedDate($datesCollection->last());
        $datesCollection = $this->generateDates($config);

        self::assertCount(0, $datesCollection);
    }

    public function test_three_days_recurrence_after_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 1, 4, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 7, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 1, 10, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 1, 13, 8), $datesCollection[3]);
    }

    public function test_three_days_recurrence_after_end_with_repeated_count(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 12, 25, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = $this->generateDates($config);

        self::assertCount(2, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 28, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[1]);

        $config->setRepeatedCount($datesCollection->count());
        $config->setLastRepeatedDate($datesCollection->last());
        $config->setEndDate(Carbon::create(2020, 12, 31));

        $datesCollection = $this->generateDates($config);

        self::assertCount(2, $datesCollection);
        self::assertEquals(Carbon::create(2020, 1, 3, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2020, 1, 6, 8), $datesCollection[1]);
    }

    public function test_every_week_recurrence_after_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::WEEK)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setRepeatIn([WeekdayEnum::MONDAY, WeekdayEnum::SUNDAY]);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 1, 7, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 13, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 1, 14, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 1, 20, 8), $datesCollection[3]);
    }

    public function test_three_weeks_recurrence_after_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::WEEK)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setRepeatIn([WeekdayEnum::MONDAY, WeekdayEnum::SUNDAY]);

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2019, 1, 21, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 27, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 2, 11, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 2, 17, 8), $datesCollection[3]);
    }

    public function test_every_month_recurrence_in_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::MONTH)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::IN)
            ->setFrequencyEndValue(Carbon::create(2019, 5, 1))
            ->setRepeatIn(31);

        $datesCollection = $this->generateDates($config);

        self::assertCount(3, $datesCollection);
        self::assertEquals(Carbon::create(2019, 2, 28, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 3, 31, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 4, 30, 8), $datesCollection[2]);
    }

    public function test_three_months_recurrence_in_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::MONTH)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::IN)
            ->setFrequencyEndValue(Carbon::create(2019, 10, 1))
            ->setRepeatIn(31);

        $datesCollection = $this->generateDates($config);

        self::assertCount(2, $datesCollection);
        self::assertEquals(Carbon::create(2019, 4, 30, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 7, 31, 8), $datesCollection[1]);
    }

    public function test_every_year_recurrence_after_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2023, 12, 31, 8));

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2020, 2, 29, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2021, 2, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2022, 2, 28, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2023, 2, 28, 8), $datesCollection[3]);
    }

    public function test_three_years_recurrence_after_end(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2031, 12, 31, 8));

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2022, 2, 28, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2025, 2, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2028, 2, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2031, 2, 28, 8), $datesCollection[3]);
    }

    public function test_three_years_recurrence_after_end_with_repeated_count(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2031, 12, 31, 8));

        $datesCollection = $this->generateDates($config);

        self::assertCount(4, $datesCollection);
        self::assertEquals(Carbon::create(2022, 2, 28, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2025, 2, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2028, 2, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2031, 2, 28, 8), $datesCollection[3]);

        $config->setRepeatedCount(4);
        $datesCollection = $this->generateDates($config);

        self::assertCount(0, $datesCollection);
    }

    public function test_invalid_frequency_end_value(): void
    {
        $config = new RecurringConfig ();

        $config->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $this->expectException(InvalidFrequencyEndValue::class);

        $this->generateDates($config);
    }
}
