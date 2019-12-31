<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\RecurringBuilder;
use PhpRecurring\RecurringConfig;

class RecurringBuilderTest extends AbstractTestCase
{
    /** @test */
    public function every_day_recurrence_never_end()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[4]);
    }

    /** @test
     */
    public function every_day_recurrence_never_end_with_last_repeated_date()
    {
        $recurringConfig = new RecurringConfig ();

        $recurringConfig->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2019, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2019, 12, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2019, 12, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2019, 12, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2019, 12, 31, 8, 0, 0), $datesCollection[4]);

        $recurringConfig->setLastRepeatedDate($datesCollection->last());
        RecurringBuilder::forConfig($recurringConfig)->startRecurring();
        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(0, $datesCollection);

        $recurringConfig->setStartDate(Carbon::create(2020, 12, 26, 8, 0, 0));
        $recurringConfig->setEndDate($recurringConfig->getStartDate()->copy()->endOfYear());

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(5, $datesCollection);
        self::assertEquals(Carbon::create(2020, 12, 27, 8, 0, 0), $datesCollection[0]);
        self::assertEquals(Carbon::create(2020, 12, 28, 8, 0, 0), $datesCollection[1]);
        self::assertEquals(Carbon::create(2020, 12, 29, 8, 0, 0), $datesCollection[2]);
        self::assertEquals(Carbon::create(2020, 12, 30, 8, 0, 0), $datesCollection[3]);
        self::assertEquals(Carbon::create(2020, 12, 31, 8, 0, 0), $datesCollection[4]);

        $recurringConfig->setLastRepeatedDate($datesCollection->last());
        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertCount(0, $datesCollection);
    }

    /** @test*/
    public function invalid_frequency_end_value()
    {
        $recurringConfig = new RecurringConfig ();

        $recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::DAY())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue('invalid')
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $this->expectException(InvalidFrequencyEndValue::class);

        $datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();

        self::assertEmpty($datesCollection);
    }
}