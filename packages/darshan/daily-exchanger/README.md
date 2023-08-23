# Daily Exchanger Package

## Daily Exchanger is a package that allow you to exchange any currency in the world, it makes use of the European Central Bank daily reference.

## Installation

[PHP](https://php.net) 8.1+  and [Composer](https://getcomposer.org) are required.

To get the latest version of darshan/daily-exchanger
First download this package source code and put it in your porject packages folder.
Then add this package as local dependency in root composer.json like this

```
"repositories": [
        {
            "type": "path",
            "url": "./packages/darshan/daily-exchanger",
            "options": {
                "symlink": true
            }
        }
    ],
```

Now run following command 


```bash
composer require darshan/daily-exchanger
```

## General Usage

To exchange any currency, all you need to do his hit this endpoint '/api/daily-exchanger/currency-to' with the query string amount & currency with valid currency code 

```
daily-exchanger/currency-to?amount=500&currency=USD
```

We support several currencies, see list of currency below.

```
USD, JPY, BGN, CZK, DKK, GBP, HUF, PLN, RON, SEK, CHF, ISK, NOK, TRY, AUD, BRL, CAD, CNY, HKD, IDR, ILS, INR, KRW, MXN, MYR, NZD, PHP, SGD, THB, ZAR
```

