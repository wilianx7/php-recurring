# PHP Recurring
PHP library to make getting dates easier when working with recurring tasks.

## Installation
You can install the package via composer:

```
composer require wilianx7/php-recurring
```


## Basic usage
- Configuration for every day recurrence ending never:

```php
$recurringConfig = new RecurringConfig();

$recurringConfig->setStartDate(Carbon::create(2019, 12, 26, 8, 0, 0))
    ->setFrequencyType(FrequencyTypeEnum::DAY())
    ->setFrequencyInterval(1)
    ->setFrequencyEndType(FrequencyEndTypeEnum::NEVER())
    ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

$datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();
```


- Configuration for weekly recurrence (Monday and Sunday) ending after 5 occurrences:

```php
$recurringConfig = new RecurringConfig();

$recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
    ->setFrequencyType(FrequencyTypeEnum::WEEK())
    ->setFrequencyInterval(1)
    ->setFrequencyEndType(FrequencyEndTypeEnum::AFTER())
    ->setFrequencyEndValue(5)
    ->setRepeatIn([WeekdayEnum::MONDAY(), WeekdayEnum::SUNDAY()])
    ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

$datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();
```


- Configuration for monthly recurrence (day 27) ending in 2019-11-30:

```php
$recurringConfig = new RecurringConfig();

$recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
    ->setFrequencyType(FrequencyTypeEnum::MONTH())
    ->setFrequencyInterval(1)
    ->setFrequencyEndType(FrequencyEndTypeEnum::IN())
    ->setFrequencyEndValue(Carbon::create(2019, 11, 30))
    ->setRepeatIn(27)
    ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

$datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();
```


- Configuration for yearly recurrence (day 27 and month 10) ending never:

```php
$recurringConfig = new RecurringConfig();

$recurringConfig->setStartDate(Carbon::create(2019, 1, 1, 8, 0, 0))
    ->setFrequencyType(FrequencyTypeEnum::MONTH())
    ->setFrequencyInterval(1)
    ->setFrequencyEndType(FrequencyEndTypeEnum::IN())
    ->setFrequencyEndValue(Carbon::create(2019, 11, 30))
    ->setRepeatIn(['day' => 27, 'month' => 10])
    ->setEndDate(Carbon::create(2019, 12, 31, 23, 59, 59));

$datesCollection = RecurringBuilder::forConfig($recurringConfig)->startRecurring();
```


## Recurring Config

| **Attribute** | **Description** | **Default** |
| :--- | :--- | :--- |
| `startDate` | Date the search will start for find the dates the recurrence should be generated. | Start of current year |
| `endDate` | Date the search for the dates will ended. | End of current year |
| `frequencyType` | How often the recurrence will be generated accord of the enum FrequencyTypeEnum. Can be setted as: **DAY**, **WEEK**, **MONTH** or **YEAR**. | FrequencyTypeEnum::DAY() |
| `frequencyInterval` | Determines the interval between recurrences according to the chosen frequency type. | 1 |
| `repeatIn` | Determines when recurrence should be generated according to the frequency type chosen. Can be setted, for example, as: null (for **DAY**); [ WeekdayEnum::MONDAY(), WeekdayEnum::SUNDAY() ] (for **WEEK**); 31 (for **MONTH**); ['day' => 31, 'month' => 2] (for **YEAR**)." | Null |
| `frequencyEndType` | Determines what will be the stopping criterion for recurrence generation according of the enum FrequencyEndTypeEnum. Can be setted as: **NEVER**, **IN** or **AFTER**. | FrequencyEndTypeEnum::NEVER() |
| `frequencyEndValue` | Determines a value according to the chosen stop criterion. Can be setted, for example, as: null (for NEVER); 3 (for AFTER); Carbon::now() (for IN). | Null |
| `lastRepeatedDate` | Date the last recurrence was generated. It is used to avoid unnecessary date generation by calling the generation method more than once. | Null |
| `repeatedCount` | How many recurrences have already been generated. It is used to avoid unnecessary date generation by calling the generation method more than once. | Null |
| `exceptDates` | Dates when recurrence should not be generated even if the date conforms to the specified setting. Accepts native array or Laravel Collection. | Null |
| `includeStartDate` | Defines whether the start date should be included in the return array | false |

* **includeStartDate**: By default, the start date is not included in the return array, as it assumes that this date is already in use, requiring only the return of subsequent dates. 
However, you can override this behavior by setting "includeStartDate" property as true.

## Recurring Builder

| **Method** | **Description** | **Return** |
| :--- | :--- | :--- |
| `forConfig` | Used to construct the recurrence according to  setting passed by parameter. | Self |
| `startRecurring` | Start generating dates for recurrence | A collection with all the dates generated |


## Enums

| **Enum** | **Values** |
| :--- | :--- |
| `FrequencyEndTypeEnum` | NEVER, IN, AFTER |
| `FrequencyTypeEnum` | DAY, WEEK, MONTH, YEAR |
| `WeekdayEnum` | SUNDAY, MONDAY, TUESDAY, WEDNESDAY, THURSDAY, FRIDAY, SATURDAY |


## Testing

```
./vendor/bin/phpunit (Linux)
```

```
.\vendor\bin\phpunit (Windows)
```
