<?php

namespace Darshan\DailyExchanger;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Darshan\DailyExchanger\Exceptions\InvalidCurrencyException;
use Darshan\DailyExchanger\Exceptions\InvalidAmountException;

class DailyExchanger
{
    /**
     * @var string
     */
    protected $url = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    protected string $cacheKey = 'daily-exchanger';

    protected string $defaultCurrency = 'EUR';

    protected function fetchAndCacheRates(): void
    {
        $client = new Client;
        $response = $client->get($this->url);

        $xml = simplexml_load_string($response->getBody());
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        $rates = $array['Cube']['Cube']['Cube'];

        $rates = array_reduce($rates, function($carry, $rate) {

            $carry[$rate['@attributes']['currency']] = (float) $rate['@attributes']['rate'];

            return $carry;
        }, []);

        Cache::put($this->cacheKey.'.rates', $rates);
    }
   
   public function convertTo(string $currency, $amount)
   {
        if (!is_numeric($amount)) {
            throw new InvalidAmountException($amount.' is not a valid amount.');   
        }

        $dayOf = Cache::get($this->cacheKey.'.date');

        if ($dayOf != now()->format("Y-m-d") || !Cache::has($this->cacheKey.'.rates')) {
            $dayOf = now()->format("Y-m-d");
            Cache::put($this->cacheKey.'.date', now()->format("Y-m-d"));
            $this->fetchAndCacheRates();
        }

        $rates = Cache::get($this->cacheKey.'.rates');

        //check if user passed a valid currency
        if (! array_key_exists($currency, $rates)) {
            throw new InvalidCurrencyException($currency.' is not a valid currency.');
        }

        return $amount * $rates[$currency];
   }

}