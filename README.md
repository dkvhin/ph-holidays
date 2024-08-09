# Fetch Philippine Holidays

This package can fetch regular and special holidays from the official website
https://www.officialgazette.gov.ph/nationwide-holidays/ 

```php
use Dkvhin\PhHolidays\PhilippineHolidays;


// for the current year
$holidays = PhilippineHolidays::fetch();

// returns an array of regular holidays
$regular = $holidays->regular();

// returns an array of special holidays
$special = $holidays->special();
```

## Installation

You can install the package via composer:

```bash
composer require dkvhin/ph-holidays
```

## Usage


```php
use Dkvhin\PhHolidays\PhilippineHolidays;

// for the current year
$holidays = PhilippineHolidays::fetch();


// you can also pass specific year
// NOTE that the website only provides the last 6 years worth of holidays
// Advance year are not always available from the website 
// eg. ( current year is 2024, holidays for 2025 might not be available yet until the end of the year)
$holidays = PhilippineHolidays::fetch(2022);

```

## Testing

```bash
composer test
```

## Credits

- [Ervin Musngi](https://github.com/dkvhin)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.