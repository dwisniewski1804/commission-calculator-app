<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\CommissionCalculator\Exception;

use Exception;
use Throwable;

class InvalidAmountException extends Exception
{
    public function __construct($amount, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Amount: $amount is invalid", $code, $previous);
    }
}
