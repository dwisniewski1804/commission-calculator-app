<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\CommissionCalculator;

use App\CommissionTask\Entity\Operation;

/**
 * Calculates commission based on current operation, payload and key of current operation in the collection.
 */
interface CommissionCalculatorInterface
{
    public function __construct();

    public function calculate(Operation $operation, array $payload, int $key): string;
}
