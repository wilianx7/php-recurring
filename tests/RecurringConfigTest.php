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
    /** @test */
    public function invalid_frequency_interval()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyInterval(-1);

        $this->expectException(InvalidFrequencyInterval::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function invalid_frequency_end_value_after()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::AFTER());

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function invalid_frequency_end_value_in()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::IN());

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function invalid_frequency_end_value_not_carbon_instance()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::IN())
            ->setFrequencyEndValue(2);

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function invalid_frequency_end_value_carbon_instance()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(Carbon::now());

        $this->expectException(InvalidFrequencyEndValue::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function invalid_repeated_count()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setRepeatedCount(-1);

        $this->expectException(InvalidRepeatedCount::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function invalid_repeat_in_year()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setFrequencyType(FrequencyTypeEnum::YEAR())
            ->setRepeatIn(['day' => 2, 'test' => 4]);

        $this->expectException(InvalidRepeatIn::class);
        self::assertFalse($recurringConfig->isValid());
    }

    /** @test */
    public function valid_configuration()
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
            ->setFrequencyType(FrequencyTypeEnum::YEAR())
            ->setFrequencyInterval(3)
            ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
            ->setFrequencyEndValue(4)
            ->setRepeatIn(['day' => 31, 'month' => 2])
            ->setEndDate(Carbon::create(2031, 12, 31, 8, 0, 0));

        self::assertTrue($recurringConfig->isValid());
    }
}