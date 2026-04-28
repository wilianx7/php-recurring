<?php

namespace PhpRecurring;

use PhpRecurring\Actions\GenerateRecurringDatesAction;
use PhpRecurring\Actions\Configurations\NormalizeRecurringConfigAction;
use PhpRecurring\Actions\Configurations\ValidateRecurringConfigAction;
use PhpRecurring\Exceptions\InvalidFrequencyEndValue;
use PhpRecurring\Exceptions\InvalidFrequencyInterval;
use PhpRecurring\Exceptions\InvalidRepeatedCount;
use PhpRecurring\Exceptions\InvalidRepeatIn;

class RecurringBuilder
{
    public function __construct(
        private RecurringConfig $recurringConfig,
        private ValidateRecurringConfigAction $validateRecurringConfigAction = new ValidateRecurringConfigAction(),
        private NormalizeRecurringConfigAction $normalizeRecurringConfigAction = new NormalizeRecurringConfigAction(),
        private GenerateRecurringDatesAction $generateRecurringDatesAction = new GenerateRecurringDatesAction()
    ) {
    }

    public static function forConfig(RecurringConfig $recurringConfig): self
    {
        return new self($recurringConfig);
    }

    /**
     * @throws InvalidFrequencyInterval
     * @throws InvalidFrequencyEndValue
     * @throws InvalidRepeatIn
     * @throws InvalidRepeatedCount
     */
    public function startRecurring(): array
    {
        $this->validateRecurringConfigAction->execute($this->recurringConfig);

        return $this->generateRecurringDatesAction->execute(
            $this->normalizeRecurringConfigAction->execute($this->recurringConfig)
        );
    }
}
