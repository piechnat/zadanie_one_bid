<?php

namespace App\Service;

use DateTimeInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FrankfurterAPIService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getRates(DateTimeInterface $date): array
    {
        $ymd_date = $date->format('Y-m-d');

        $response = $this->client->request(
            'GET',
            'https://api.frankfurter.app/'.$ymd_date.'?base=PLN&symbols=EUR,USD,GBP,CZK',
            ['query' => ['format' => 'json']]
        );

        return $response->toArray()['rates'];
    }
}
