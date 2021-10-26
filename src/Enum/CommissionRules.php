<?php

declare(strict_types=1);

namespace App\CommissionTask\Enum;

use App\CommissionTask\Service\CommissionCalculator\Priv\PrivateWithdrawCommissionCalculator;

class CommissionRules
{
    const RULES = [
        ClientType::PRIVATE => [
            OperationType::WITHDRAW => PrivateWithdrawCommissionCalculator::class,
            OperationType::DEPOSIT => '0.0003',
        ],
        ClientType::BUSINESS => [
            OperationType::WITHDRAW => '0.005',
            OperationType::DEPOSIT => '0.0003',
        ],
    ];
}
