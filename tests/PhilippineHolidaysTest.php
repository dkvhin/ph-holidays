<?php

use Carbon\CarbonImmutable;
use Dkvhin\PhHolidays\PhilippineHolidays;
use Dkvhin\PhHolidays\Exceptions\InvalidYear;

// it('can get holidays on the current year', function () {
//     $fetched = PhilippineHolidays::fetch();
//     $holidays = $fetched->regular();
//     expect($holidays)
//         ->toBeArray()
//         ->not()->toBeEmpty();

//     $holidays = $fetched->special();
//     expect($holidays)
//         ->toBeArray()
//         ->not()->toBeEmpty();
// });

it('cannot get holidays more than 6 years old from the current year', function () {
    PhilippineHolidays::fetch(CarbonImmutable::now()->addYear(-7)->year);
})->throws(InvalidYear::class, 'is too low');

it('cannot get holidays that are 2 years ahead from the current year', function () {
    PhilippineHolidays::fetch(CarbonImmutable::now()->addYear(2)->year);
})->throws(InvalidYear::class, 'is too high');