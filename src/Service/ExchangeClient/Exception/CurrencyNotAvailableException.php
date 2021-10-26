<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\ExchangeClient\Exception;

use Exception;
use Throwable;

class CurrencyNotAvailableException extends Exception
{
    public function __construct(string $currency, $code = 0, Throwable $previous = null)
    {
        $message = "The $currency currency is not supported.";
        parent::__construct($message, $code, $previous);
    }
}
