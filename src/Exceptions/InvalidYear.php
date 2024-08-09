<?php

namespace Dkvhin\PhHolidays\Exceptions;

use RuntimeException;

class InvalidYear extends RuntimeException
{
    public static function notFound(int $year): self
    {
        return new self("Year `{$year}` not found");
    }

    public static function yearTooLow(int $suppliedYear, int $minimumYear): self
    {
        return new self("Year `{$suppliedYear}` is too low, current miniumum year is `{$minimumYear}`");
    }

    public static function yearTooHigh(int $suppliedYear, int $maximumYear): self
    {
        return new self("Year `{$suppliedYear}` is too high, maximum year is `{$maximumYear}`");
    }
}
