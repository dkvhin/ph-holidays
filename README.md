# Fetch Philippine Holidays

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dkvhin/ph-holidays.svg?style=flat-square)](https://packagist.org/packages/dkvhin/ph-holidays)
[![Tests](https://img.shields.io/github/actions/workflow/status/dkvhin/ph-holidays/php.yml?branch=main&label=tests&style=flat-square)](https://github.com/dkvhin/ph-holidays/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/dkvhin/ph-holidays.svg?style=flat-square)](https://packagist.org/packages/dkvhin/ph-holidays)


This package can fetch regular and special holidays from the official website
https://www.officialgazette.gov.ph/nationwide-holidays/ 

NOTE: This is not yet working 100% as the website is using a bot checker ( cloudflare ), this can cause errors if used in production.

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
