<?php


namespace PhpRecurring\Enums;

enum FrequencyEndTypeEnum: string
{
    case NEVER = 'NEVER';
    case IN = 'IN';
    case AFTER = 'AFTER';
}