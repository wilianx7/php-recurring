<?php

namespace PhpRecurring\Enums;

enum FrequencyTypeEnum: string
{
    case DAY = 'DAY';
    case WEEK = 'WEEK';
    case MONTH = 'MONTH';
    case YEAR = 'YEAR';
}