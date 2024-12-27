<?php

use GuzzleHttp\Client;
use Carbon\CarbonImmutable;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Dkvhin\PhHolidays\PhilippineHolidays;
use Dkvhin\PhHolidays\Sources\TimeAndDate;
use GuzzleHttp\Exception\RequestException;
use Dkvhin\PhHolidays\Exceptions\InvalidYear;
use Dkvhin\PhHolidays\Sources\OfficeHolidays;

it('can get holidays on the current year', function () {

    // Create a mock and queue two responses.
    $mock = new MockHandler([
        new Response(200, ['X-Foo' => 'Bar'], file_get_contents("./tests/html/holidays.html"))
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler'  => $handlerStack]);
    $fetched = PhilippineHolidays::fetch(null, $client);

    $holidays = $fetched->regular();
    expect($holidays)
        ->toBeArray()
        ->not()->toBeEmpty();

    $holidays = $fetched->special();
    expect($holidays)
        ->toBeArray()
        ->not()->toBeEmpty();
});

it('can get holidays on the current year 2', function () {

    // Create a mock and queue two responses.
    $mock = new MockHandler([
        new Response(200, ['X-Foo' => 'Bar'], file_get_contents("./tests/html/holidays2.html"))
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler'  => $handlerStack]);
    $fetched = TimeAndDate::fetch(null, $client);

    $holidays = $fetched->regular();
    expect($holidays)
        ->toBeArray()
        ->not()->toBeEmpty();

    $holidays = $fetched->special();
    expect($holidays)
        ->toBeArray()
        ->not()->toBeEmpty();
});

it('can get holidays on the current year 3', function () {

    // Create a mock and queue two responses.
    // $mock = new MockHandler([
    //     new Response(200, ['X-Foo' => 'Bar'], file_get_contents("./tests/html/holidays2.html"))
    // ]);

    // $handlerStack = HandlerStack::create($mock);
    // $client = new Client(['handler'  => $handlerStack]);
    $fetched = OfficeHolidays::fetch(2025);

    $holidays = $fetched->regular();
    expect($holidays)
        ->toBeArray()
        ->not()->toBeEmpty();

    $holidays = $fetched->special();
    expect($holidays)
        ->toBeArray()
        ->not()->toBeEmpty();
});

it('cannot get holidays more than 6 years old from the current year', function () {
    PhilippineHolidays::fetch(CarbonImmutable::now()->addYear(-7)->year);
})->throws(InvalidYear::class, 'is too low');

it('cannot get holidays that are 2 years ahead from the current year', function () {
    PhilippineHolidays::fetch(CarbonImmutable::now()->addYear(2)->year);
})->throws(InvalidYear::class, 'is too high');
