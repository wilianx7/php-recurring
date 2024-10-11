<?php


namespace PhpRecurring\Tests;


use Carbon\Carbon;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\Enums\FrequencyTypeEnum;
use PhpRecurring\RecurringConfig;
use Illuminate\Support\Collection;

class ShouldGenerateTest extends AbstractTestCase
{
    public function test_every_day_recurrence_without_last_date_never_end(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::createFromDate(2019, 12, 26))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

        $currentDate = Carbon::createFromDate(2019, 12, 26)->addDay();
        $datesCollection = new Collection();

        self::assertTrue($this->shouldGenerate($recurringConfig, $currentDate, $datesCollection));
    }

    public function test_every_day_recurrence_with_last_date_never_end(): void
    {
        $recurringConfig = new RecurringConfig();

        $recurringConfig->setStartDate(Carbon::createFromDate(2019, 12, 26))
            ->setFrequencyType(FrequencyTypeEnum::DAY)
            ->setFrequencyInterval(1)
            ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER)
            ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59))
            ->setLastRepeatedDate(Carbon::createFromDate(2019, 12, 26)->endOfYear());

        $currentDate = Carbon::createFromDate(2019, 12, 26)->addDay();
        $datesCollection = new Collection();

        self::assertFalse($this->shouldGenerate($recurringConfig, $currentDate, $datesCollection));
    }
}
