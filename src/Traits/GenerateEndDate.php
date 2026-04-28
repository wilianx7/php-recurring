<?php

namespace PhpRecurring\Traits;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use DateTimeInterface;
use Exception;
use PhpRecurring\Enums\FrequencyEndTypeEnum;

trait GenerateEndDate
{
    protected function generateEndDate(
        DateTimeInterface|string|int|null $frequencyEndValue,
        FrequencyEndTypeEnum $frequencyEndType
    ): ?Carbon {
        if ($frequencyEndValue) {
            if ($frequencyEndType == FrequencyEndTypeEnum::AFTER) {
                return null;
            }

            if ($frequencyEndType == FrequencyEndTypeEnum::IN) {
                try {
                    if ($frequencyEndValue instanceof DateTimeInterface) {
                        return Carbon::instance($frequencyEndValue);
                    }

                    return Carbon::createFromDate($frequencyEndValue);
                } catch (Exception) {
                    throw new InvalidDateException('frequencyEndValue', $frequencyEndValue);
                }
            }
        }

        return Carbon::now()->endOfYear()->setTime(23, 59, 59, 999999);
    }
}
