<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\CommissionCalculator;

use App\CommissionTask\Entity\Operation;
use App\CommissionTask\Enum\CommissionRules;
use App\CommissionTask\Service\CommissionCalculator\Exception\InvalidAmountException;
use App\CommissionTask\Service\Math;

/**
 * Decides what kind of commission will be charged.
 */
class BasicCommissionCalculator
{
    /**
     * @var Math
     */
    private $math;

    public function __construct()
    {
        $this->math = new Math(2);
    }

    /**
     * @throws InvalidAmountException
     */
    public function calculate(Operation $operation, array $payload, int $key): string
    {
        if ((float) $operation->getAmount() < 0) {
            throw new InvalidAmountException($operation->getAmount());
        }
        $rule = CommissionRules::RULES[$operation->getUserType()][$operation->getOperationType()];
        if (class_exists($rule)) {
            $concreteCalculator = (new $rule());

            return $concreteCalculator->calculate($operation, $payload, $key);
        }

        return $this->math->multiply($rule, $operation->getAmount());
    }
}
