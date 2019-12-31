<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use PhpRecurring\Enums\FrequencyEndTypeEnum;

class GenerateEndDateTest extends AbstractTestCase
{
    /** @test */
    public function never_end_type_end_value_null()
    {
        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(null, FrequencyEndTypeEnum::NEVER()));
    }

    /** @test */
    public function never_end_type_end_value_two()
    {

        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(2, FrequencyEndTypeEnum::NEVER()));
    }

    /** @test */
    public function after_end_type_end_value_null()
    {

        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(null, FrequencyEndTypeEnum::AFTER()));
    }

    /** @test */
    public function after_end_type_end_value_five()
    {

        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(5, FrequencyEndTypeEnum::AFTER()));
    }

    /** @test */
    public function in_end_type_end_value_null()
    {

        self::assertEquals(Carbon::now()->endOfYear(), $this->generateEndDate(null, FrequencyEndTypeEnum::IN()));
    }

    /** @test */
    public function in_end_type_end_value_set()
    {

        self::assertEquals(Carbon::create(2020, 1, 1), $this->generateEndDate(Carbon::create(2020, 1, 1), FrequencyEndTypeEnum::IN()));
        self::assertEquals(Carbon::create(2020, 1, 1, 8, 0, 0), $this->generateEndDate('2020-01-01 08:00:00', FrequencyEndTypeEnum::IN()));
    }

    /** @test */
    public function in_end_type_invalid_end_value()
    {
        $this->expectException(InvalidDateException::class);

        $this->generateEndDate('123;adfsafs;a', FrequencyEndTypeEnum::IN());
    }
}