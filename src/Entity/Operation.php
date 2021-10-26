<?php

declare(strict_types=1);

namespace App\CommissionTask\Entity;

use DateTime;
use Exception;

/**
 * Entity that stores single Operation data. It's immutable so after constructing it You can not modify its values.
 */
class Operation
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $operationType;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @throws Exception
     */
    public function __construct(string $date, string $userId, string $userType, string $operationType, string $amount, string $currency)
    {
        $this->date = new DateTime($date);
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
