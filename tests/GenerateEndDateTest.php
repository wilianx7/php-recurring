<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use PhpRecurring\Enums\FrequencyEndTypeEnum;

class GenerateEndDateTest extends AbstractTestCase
{
    public function test_never_end_type_end_value_null(): void
    {
        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(null, FrequencyEndTypeEnum::NEVER));
    }

    public function test_never_end_type_end_value_two(): void
    {
        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(2, FrequencyEndTypeEnum::NEVER));
    }

    public function test_after_end_type_end_value_null(): void
    {
        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(null, FrequencyEndTypeEnum::AFTER));
    }

    public function test_after_end_type_end_value_five(): void
    {
        self::assertNull($this->generateEndDate(5, FrequencyEndTypeEnum::AFTER));
    }

    public function test_in_end_type_end_value_null(): void
    {

        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(null, FrequencyEndTypeEnum::IN));
    }

    public function test_in_end_type_end_value_set(): void
    {
        self::assertEquals(
            Carbon::create(2020),
            $this->generateEndDate(Carbon::create(2020), FrequencyEndTypeEnum::IN)
        );

        self::assertEquals(
            Carbon::create(2020, 1, 1, 8),
            $this->generateEndDate('2020-01-01 08:00:00', FrequencyEndTypeEnum::IN)
        );
    }

    public function test_in_end_type_invalid_end_value(): void
    {
        $this->expectException(InvalidDateException::class);

        $this->generateEndDate('123;adfsafs;a', FrequencyEndTypeEnum::IN);
    }
}