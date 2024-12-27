<?php

namespace Dkvhin\PhHolidays\Sources;

use DOMElement;
use DOMDocument;
use DOMNodeList;
use Carbon\CarbonImmutable;
use Dkvhin\PhHolidays\Exceptions\InvalidYear;

class TimeAndDate
{
    /**
     * @param array<int<0, max>, array{name: string, date: string}> $regularHolidays
     * @param array<int<0, max>, array{name: string, date: string}> $specialHolidays
     */
    protected  function __construct(
        protected array $regularHolidays,
        protected array $specialHolidays,
    ) {}


    public static function fetch(?int $currentYear = null, ?\GuzzleHttp\Client $client = null, bool $isRetry = false): static
    {
        $currentYear ??= CarbonImmutable::now()->year;

        self::ensureYearCanBeFetched($currentYear);

        $endpoint = "https://www.timeanddate.com/holidays/philippines/{$currentYear}";
        $client ??= new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $endpoint, [
                'headers'   => [
                    'User-Agent'                    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36 Edg/127.0.0.0',
                    'upgrade-insecure-requests'     => 1,
                    'Accept'                        => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Encoding'               => 'gzip, deflate, zstd',
                    'Accept-Language'               => 'en-US,en;q=0.9,vi;q=0.8,es;q=0.7,ar;q=0.6',
                    'Cache-Control'                 => 'no-cache'
                ]
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode != 200) {
                throw InvalidYear::notFound($currentYear);
            }

            $content = $response->getBody()->getContents();
            $dom = new DOMDocument();
            $dom->loadHTML($content, LIBXML_NOERROR);
            $dom->preserveWhiteSpace = false;

            $regularHolidays = [];
            $specialHolidays = [];

            // Regular Holiday
            $table = $dom->getElementById('holidays-table');
    
            if ($table) {
                $tbody = $table->getElementsByTagName('tbody');
                if ($item = $tbody->item(0)) {
                    $rows = $item->getElementsByTagName('tr');
                    $regularHolidays = self::formatHoliday($rows, $currentYear, 'Regular Holiday');
                }
            }


            $table = $dom->getElementById('holidays-table');

            if ($table) {
                $tbody = $table->getElementsByTagName('tbody');
                if ($item = $tbody->item(0)) {
                    $rows = $item->getElementsByTagName('tr');
                    $specialHolidays = self::formatHoliday($rows, $currentYear, 'Special Non-working Holiday');
                }
            }

            return new static($regularHolidays, $specialHolidays);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {

            // if 403, it is either this is the first request
            // usually it is blocked on the first request
            // we can retry again to request the page
            if ($ex->getCode() === 403 && !$isRetry) {
                return self::fetch($currentYear, $client, true);
            }

            throw InvalidYear::error($currentYear, $ex);
        }
    }

    /**
     * @param DOMNodeList<DOMElement> $rows
     * @return array<int<0, max>, array{name: string, date: string}>
     */
    private static function formatHoliday(DOMNodeList $rows, int $currentYear, string $type): array
    {
        $response = [];
        foreach ($rows as $row) {
            $colsTh = $row->getElementsByTagName('th');

            if(count($colsTh) < 1) {
                continue;
            }

            $cols = $row->getElementsByTagName('td');

            if(count($cols) < 1) {
                continue;
            }

            if (strtolower($type) != strtolower(trim($cols[2]->nodeValue))) {
                continue;
            }

            $response[] = [
                'name'  => trim($cols[1]->getElementsByTagName('a')[0]->nodeValue),
                'date'  => CarbonImmutable::parse(trim($colsTh[0]->nodeValue) . ' ' . $currentYear)->format('M d, Y')
            ];
        }

        return $response;
    }

    private static function ensureYearCanBeFetched(int $year): void
    {
        /**
         * Philippine government holidays only provide the previous 6 years from the current Year
         */
        $minimumYears = CarbonImmutable::now()->addYears(-6)->year;

        if ($year < $minimumYears) {
            throw InvalidYear::yearTooLow($year, $minimumYears);
        }

        /**
         * Philippine government doesn't provide not more than 1 year
         */
        $maximumYears = CarbonImmutable::now()->addYears(1)->year;
        if ($year > $maximumYears) {
            throw InvalidYear::yearTooHigh($year, $maximumYears);
        }
    }

    /**
     * @return array<int<0, max>, array{name: string, date: string}>
     */
    public function regular(): array
    {
        return $this->regularHolidays;
    }

    /**
     * @return array<int<0, max>, array{name: string, date: string}>
     */
    public function special(): array
    {
        return $this->specialHolidays;
    }
}
