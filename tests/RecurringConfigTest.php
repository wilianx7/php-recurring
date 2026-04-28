<?php

namespace PhpRecurring\Tests;

use Carbon\Carbon;
use DateTimeImmutable;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\Exceptions\InvalidFrequencyInterval;
use PhpRecurring\Exceptions\InvalidRepeatedCount;
use PhpRecurring\Exceptions\InvalidRepeatIn;
use PhpRecurring\RecurringConfig;

class RecurringConfigTest extends AbstractTestCase
{
    public function test_invalid_frequency_interval(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyInterval(-1);

        $this->expectException(InvalidFrequencyInterval::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_invalid_frequency_end_value_after(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::AFTER);

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_invalid_frequency_end_value_in(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::IN);

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_invalid_frequency_end_value_not_carbon_instance(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::IN)
            ->setFrequencyEndValue(2);

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_invalid_frequency_end_value_carbon_instance(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(Carbon::now());

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_invalid_repeated_count(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setRepeatedCount(-1);

        $this->expectException(InvalidRepeatedCount::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_invalid_repeat_in_year(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setRepeatIn(['day' => 2, 'test' => 4]);

        $this->expectException(InvalidRepeatIn::class);
        self::assertFalse($recurringConfig->isValid());
    }

    public function test_valid_configuration(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::YEAR)
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER)
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2031, 12, 31, 8, 0, 0));

        self::assertTrue($recurringConfig->isValid());
    }

    public function test_accepts_datetime_interface_in_public_setters(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(new DateTimeImmutable('2019-01-01 08:00:00'))
            ->setEndDate(new DateTimeImmutable('2019-12-31 23:59:59'))
            ->setFrequencyEndType(FrequencyEndTypeEnum::IN)
            ->setFrequencyEndValue(new DateTimeImmutable('2019-11-30 00:00:00'))
            ->setLastRepeatedDate(new DateTimeImmutable('2019-06-01 12:00:00'))
            ->setExceptDates([new DateTimeImmutable('2019-02-01 08:30:00')]);

        self::assertEquals(Carbon::create(2019, 1, 1, 8, 0, 0), $recurringConfig->getStartDate());
        self::assertEquals(Carbon::create(2019, 12, 31, 23, 59, 59), $recurringConfig->getEndDate());
        self::assertEquals(Carbon::create(2019, 11, 30, 0, 0, 0), $recurringConfig->getFrequencyEndValue());
        self::assertEquals(Carbon::create(2019, 6, 1, 12, 0, 0), $recurringConfig->getLastRepeatedDate());
        self::assertEquals([Carbon::create(2019, 2, 1, 0, 0, 0)], $recurringConfig->getExceptDates());
    }
}
