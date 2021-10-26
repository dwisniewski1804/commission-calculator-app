<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\ExchangeClient;

use App\CommissionTask\Enum\StandardCurrency;
use App\CommissionTask\Service\ExchangeClient\Exception\CurrencyNotAvailableException;
use App\CommissionTask\Service\ExchangeClient\Exception\ExchangeClientException;
use App\CommissionTask\Service\Math;

/**
 * Takes any currency and exchanges it to EUR based on https://api.exchangeratesapi.io/latest.
 */
class ExchangeClient
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Math
     */
    private $math;

    public function __construct()
    {
        // I tried to install https://packagist.org/packages/vlucas/phpdotenv, but it was useless with just CMD
        //getenv('CURRENCY_LAYER_URL');
        $this->endpoint = 'http://api.exchangeratesapi.io/v1/latest';
        //getenv('CURRENCY_LAYER_API_KEY');
        $this->apiKey = 'e67f0b72ee324e8077137760323a9173';
        $this->math = new Math(2);
    }

    /**
     * @throws CurrencyNotAvailableException
     * @throws ExchangeClientException
     */
    public function exchange(string $amount, string $currency, string $exchangeCurrency): string
    {
        $rates = $this->getCurrencies($exchangeCurrency);
        // it looks strange but if API just offers one side rates I have to flip the key into expected currency instead of EURO (EURO would be always 1:1)
        $finalExchangedCurrency = $exchangeCurrency === StandardCurrency::STANDARD_CURRENCY ? $currency : $exchangeCurrency;
        if (isset($rates[$finalExchangedCurrency])) {
            $rate = (string) $rates[$finalExchangedCurrency];

            if ($exchangeCurrency === StandardCurrency::STANDARD_CURRENCY) {
                return $this->math->divide($amount, $rate);
            }

            return $this->math->multiply($amount, $rate);
        }

        throw new CurrencyNotAvailableException($currency);
    }

    /**
     * API does not support base currency restriction on free plan, so I decided to rely only on EUR as a base in both directions of exchange.
     *
     * @throws ExchangeClientException
     */
    private function getCurrencies(string $exchangeCurrency): array
    {
        /**
         * API does not support base currency restriction on free plan, so I decided to rely only on EUR as a base in both directions of exchange.
         */
        //.'&base='.$exchangeCurrency;
        $url = $this->endpoint.'?access_key='.$this->apiKey;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode >= 200 && $httpcode < 300) {
            return json_decode($data, true)['rates'];
        } else {
            throw new ExchangeClientException('Rating server is not responding.');
        }
    }
}
