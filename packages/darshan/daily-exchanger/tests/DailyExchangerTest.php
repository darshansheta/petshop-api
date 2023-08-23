<?php

namespace Darshan\DailyExchanger\Tests;

use Tests\TestCase;
use Darshan\DailyExchanger\DailyExchanger;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Darshan\DailyExchanger\Exceptions\InvalidAmountException;
use Darshan\DailyExchanger\Exceptions\InvalidCurrencyException;

class DailyExchangerTest extends TestCase
{
    protected $mockedResult = [
        'USD' => 1.0887,
        'JPY' => 158.7,
        'BGN' => 1.9558,
        'CZK' => 24.0,
        'DKK' => 7.4535,
        'GBP' => 0.85288,
        'HUF' => 382.03,
        'PLN' => 4.4643,
        'RON' => 4.9376,
        'SEK' => 11.863,
        'CHF' => 0.9565,
        'ISK' => 143.7,
        'NOK' => 11.5195,
        'TRY' => 29.6136,
        'AUD' => 1.6878,
        'BRL' => 5.3891,
        'CAD' => 1.4716,
        'CNY' => 7.9387,
        'HKD' => 8.5328,
        'IDR' => 16699.27,
        'ILS' => 4.1069,
        'INR' => 90.322,
        'KRW' => 1454.59,
        'MXN' => 18.4223,
        'MYR' => 5.0603,
        'NZD' => 1.8264,
        'PHP' => 61.251,
        'SGD' => 1.4754,
        'THB' => 38.126,
        'ZAR' => 20.3932,
    ];

    public function setUp(): void
    {
        parent::setUp();
        // here even we can register fake service provider
        $this->instance(
            DailyExchanger::class,
            Mockery::mock(DailyExchanger::class, function (MockInterface $mock) {
                $mock->shouldReceive('fetchRates')
                    ->andReturn($this->mockedResult);
                $mock->shouldReceive('getRates')
                    ->andReturn($this->mockedResult);
            })->makePartial()
        );
    }

    /** @test */
    public function test_can_convert_valid_currency()
    {
        Cache::spy();

        $dailyExchanger = resolve(DailyExchanger::class);

        $response = $dailyExchanger->convertTo('USD', 1);

        $this->assertEquals($response, $this->mockedResult['USD']);

        Cache::shouldReceive('has');

        Cache::shouldReceive('put')
                    ->with('daily-exchanger.date', date('Y-m-d'));

        $response = $dailyExchanger->convertTo('USD', 10);
    }

    /** @test */
    public function test_can_throw_InvalidAmountException()
    {
        $this->expectException(InvalidAmountException::class);

        $dailyExchanger = resolve(DailyExchanger::class);

        $response = $dailyExchanger->convertTo('USD', "ABC");
    }

    /** @test */
    public function test_can_throw_InvalidCurrencyException()
    {
        $this->expectException(InvalidCurrencyException::class);

        $dailyExchanger = resolve(DailyExchanger::class);

        $response = $dailyExchanger->convertTo('ABC', 123);
    }
}
