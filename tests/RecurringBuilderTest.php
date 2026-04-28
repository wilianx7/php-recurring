<?php

namespace PhpRecurring\Tests;

use Carbon\Carbon;
use DateTimeImmutable;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringBuilder;
use PhpRecurring\RecurringConfig;

class RecurringBuilderTest extends AbstractTestCase
{
    public function test_every_day_recurrence_never_end(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 12, 26, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[4]);
    }

    public function test_every_day_recurrence_never_end_with_last_repeated_date(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 12, 26, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[4]);

        $recurringConfig->setLastRepeatedDate(end($datesCollection));
        RecurringBuilder::forConfig($recurringConfig)->startRecurring();
        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(0, $datesCollection);

        $recurringConfig->setStartDate(Carbon::create(2020, 12, 26, 8));
        $recurringConfig->setEndDate($recurringConfig->getStartDate()->copy()->endOfYear());

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2020, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2020, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2020, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2020, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2020, 12, 31, 8), $datesCollection[4]);

        $recurringConfig->setLastRepeatedDate(end($datesCollection));
        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(0, $datesCollection);
    }

    public function test_invalid_frequency_end_value(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $this->expectException(InvalidFrequencyEndValue::class);

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertEmpty($datesCollection);
    }

    public function test_accepts_datetime_interface_in_builder_configuration(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(new DateTimeImmutable('2019-12-26 08:00:00'))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(new DateTimeImmutable('2019-12-31 23:59:59'));

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8), $datesCollection[4]);
    }

    public function test_continue_generation_with_last_repeated_date_in_same_year(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 1, 3, 23, 59, 59));

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(2, $datesCollection);

        $recurringConfig->setLastRepeatedDate(end($datesCollection));
        $recurringConfig->setEndDate(Carbon::create(2019, 1, 5, 23, 59, 59));

        $continuedDatesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(2, $continuedDatesCollection);
        self::assertEquals(Carbon::create(2019, 1, 4, 8), $continuedDatesCollection[0]);
        self::assertEquals(Carbon::create(2019, 1, 5, 8), $continuedDatesCollection[1]);
    }

    public function test_reusing_same_config_with_include_start_date_does_not_mutate_start_date(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 7, 26, 8))
            ->setFrequencyType(FrequencyTypeEnum::MONTH)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setRepeatIn(26)
            ->setIncludeStartDate(true);

        $firstDatesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();
        $secondDatesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertEquals(Carbon::create(2019, 7, 26, 8), $recurringConfig->getStartDate());
        self::assertEquals($firstDatesCollection, $secondDatesCollection);
    }
}
