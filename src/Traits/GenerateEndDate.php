<?php


namespace PhpRecurring\Traits;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Exception;
use PhpRecurring\Enums\FrequencyEndTypeEnum;

trait GenerateEndDate
{
    protected function generateEndDate($frequencyEndValue, FrequencyEndTypeEnum $frequencyEndType): ?Carbon
    {
        if (!$frequencyEndValue || $frequencyEndType->isEqual(FrequencyEndTypeEnum::NEVER())) {
            return Carbon::now()->endOfYear()->setTime(23, 59, 59, 999999);
        }

        if ($frequencyEndType->isEqual(FrequencyEndTypeEnum::IN())) {
            try {
                if ($frequencyEndValue instanceof Carbon) {
                    return Carbon::instance($frequencyEndValue);
                }

                return Carbon::createFromDate($frequencyEndValue);
            } catch (Exception $invalidDateException) {
                throw new InvalidDateException('frequencyEndValue', $frequencyEndValue);
            }
        } else if ($frequencyEndType->isEqual(FrequencyEndTypeEnum::AFTER())) {
            return null;
        }

        return Carbon::now()->endOfYear()->setTime(23, 59, 59, 999999);
    }
}