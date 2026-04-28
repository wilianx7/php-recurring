<?php

namespace PhpRecurring\Tests;

use Carbon\Carbon;
use DateTimeInterface;
use PhpRecurring\Actions\Configurations\NormalizeRecurringConfigAction;
use PhpRecurring\Actions\Configurations\ValidateRecurringConfigAction;
use PhpRecurring\Actions\GenerateEndDateAction;
use PhpRecurring\Actions\GenerateRecurringDatesAction;
use PhpRecurring\Actions\MatchRecurringDateAction;
use PhpRecurring\Actions\ShouldGenerateDateAction;
use PhpRecurring\Enums\FrequencyEndTypeEnum;
use PhpRecurring\RecurringConfig;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function generateDates(RecurringConfig $config): array
    {
        (new ValidateRecurringConfigAction())->execute($config);

        return (new GenerateRecurringDatesAction())->execute(
            (new NormalizeRecurringConfigAction())->execute($config)
        );
    }

    protected function generateEndDate(
        DateTimeInterface|string|int|null $frequencyEndValue,
        FrequencyEndTypeEnum $frequencyEndType
    ): ?Carbon {
        return (new GenerateEndDateAction())->execute($frequencyEndValue, $frequencyEndType);
    }

    protected function dateMatch(RecurringConfig $config, Carbon $currentDate, array $datesCollection): bool
    {
        return (new MatchRecurringDateAction())->execute(
            (new NormalizeRecurringConfigAction())->execute($config),
            $currentDate,
            $datesCollection
        );
    }

    protected function shouldGenerate(RecurringConfig $config, Carbon $currentDate, array $datesCollection): bool
    {
        return (new ShouldGenerateDateAction())->execute(
            (new NormalizeRecurringConfigAction())->execute($config),
            $currentDate,
            $datesCollection
        );
    }
}
