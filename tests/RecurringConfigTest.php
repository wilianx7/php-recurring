<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
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
}