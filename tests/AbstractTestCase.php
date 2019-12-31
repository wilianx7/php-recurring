<?php


namespace PhpRecurring\Tests;


use PhpRecurring\Traits\DateMatch;
use PhpRecurring\Traits\GenerateDates;
use PhpRecurring\Traits\GenerateEndDate;
use PhpRecurring\Traits\ShouldGenerate;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    use DateMatch,
        GenerateDates,
        GenerateEndDate,
        ShouldGenerate;
}