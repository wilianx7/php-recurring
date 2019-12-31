<?php


namespace PhpRecurring\Traits;


use Carbon\Carbon;
use Carbon\Exceptions\InvalidDateException;
use Exception;
use PhpRecurring\Enums\FrequencyEndTypeEnum;

trait GenerateEndDate
{
    protected function generateEndDate($frequencyEndValue, FrequencyEndTypeEnum $frequencyEndType): Carbon
    {
        if (!$frequencyEndValue || $frequencyEndType->isEqual(FrequencyEndTypeEnum::NEVER())) {
            return Carbon::now()->endOfYear();
        }

        if ($frequencyEndType->isEqual(FrequencyEndTypeEnum::IN())) {
            try {
                if ($frequencyEndValue) {
                    if ($frequencyEndValue instanceof Carbon) {
                        return Carbon::instance($frequencyEndValue);
                    }

                    return Carbon::createFromDate($frequencyEndValue);
                }
            } catch (Exception $invalidDateException) {
                throw new InvalidDateException('frequencyEndValue', $frequencyEndValue);
            }
        }

        return Carbon::now()->endOfYear();
    }
}