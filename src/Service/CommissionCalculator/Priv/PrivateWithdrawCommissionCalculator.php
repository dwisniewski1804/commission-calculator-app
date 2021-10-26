<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\CommissionCalculator\Priv;

use App\CommissionTask\Entity\Operation;
use App\CommissionTask\Enum\MoneyAmount;
use App\CommissionTask\Enum\OperationType;
use App\CommissionTask\Enum\StandardCurrency;
use App\CommissionTask\Service\CommissionCalculator\CommissionCalculatorInterface;
use App\CommissionTask\Service\ExchangeClient\Exception\CurrencyNotAvailableException;
use App\CommissionTask\Service\ExchangeClient\Exception\ExchangeClientException;
use App\CommissionTask\Service\ExchangeClient\ExchangeClient;
use App\CommissionTask\Service\Math;
use DateTime;
use Exception;

class PrivateWithdrawCommissionCalculator implements CommissionCalculatorInterface
{
    const COMMISSION_FEE = '0.003';
    const WEEK_SUM_LIMIT = '1000';

    /**
     * @var ExchangeClient
     */
    private $exchangeClient;

    /**
     * @var DateTime
     */
    private $currentWeekStart;

    /**
     * @var DateTime
     */
    private $currentWeekEnd;

    /**
     * Week sum is always counted with StandardCurrencyEnum::STANDARD_CURRENCY.
     *
     * @var string
     */
    private $thisWeekOperationsSum;

    /**
     * @var string
     */
    private $thisWeekOperationsNumber;

    /**
     * @var Math
     */
    private $math;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->exchangeClient = new ExchangeClient();
        $this->math = new Math(2);
    }

    /**
     * @throws Exception
     */
    public function calculate(Operation $operation, array $payload, int $key): string
    {
        $this->thisWeekOperationsSum = MoneyAmount::ZERO;
        $this->thisWeekOperationsNumber = 0;

        $this->collectDataFromPreviousOperations($operation, $payload, $key);
        // we use default commission fee in case of more than 3 operation in the current week
        if ($this->thisWeekOperationsNumber >= 3) {
            return $this->math->multiply(self::COMMISSION_FEE, $operation->getAmount());
        }

        return $this->calculateWithWeeklyLimit($operation);
    }

    /**
     * @throws CurrencyNotAvailableException|ExchangeClientException
     */
    private function collectDataFromPreviousOperations(Operation $operation, array $payload, int $currentOperationKey)
    {
        $this->setWeekBorders($operation->getDate());
        foreach ($payload as $loopKey => $loopOperation) {
            // we continue if key meets with the current operation or loop operation is not owned by current user or operation is not withdraw
            if ($loopKey >= $currentOperationKey
                || $operation->getUserId() !== $loopOperation->getUserId()
                || $loopOperation->getOperationType() !== OperationType::WITHDRAW) {
                continue;
            }
            $this->increaseWeekOperationsSum($loopOperation);
            $this->increaseWeekOperationsNumber($loopOperation);
        }
    }

    private function setWeekBorders(DateTime $dateTime): void
    {
        $this->currentWeekStart = clone $dateTime->modify('Monday this week');
        $this->currentWeekEnd = clone $dateTime->modify('Sunday this week')->setTime(23, 59, 59, 59);
    }

    /**
     * @throws CurrencyNotAvailableException
     * @throws ExchangeClientException
     */
    private function calculateWithWeeklyLimit(Operation $operation): string
    {
        $this->checkIfPreviousOperationsHaveExceededWeekLimit();

        $weekLimitExceededAmount = $this->calculateHowMuchIsTheLimitExceeded($operation);

        // checks if we exceed the week limit with added current operation
        if ((float) $weekLimitExceededAmount > 0.00) {
            // if currency is not STANDARD_CURRENCY we convert commission charge to its origin currency
            if ($operation->getCurrency() !== StandardCurrency::STANDARD_CURRENCY) {
                $weekLimitExceededAmount = $this->exchangeClient->exchange(
                    $weekLimitExceededAmount,
                    StandardCurrency::STANDARD_CURRENCY,
                    $operation->getCurrency()
                );
            }
            // we pay exceeded value multiplied with the commission fee
            return $this->math->multiply($weekLimitExceededAmount, self::COMMISSION_FEE);
        }

        // we pay 0 if the limit is not exceeded
        return MoneyAmount::ZERO;
    }

    private function checkIfPreviousOperationsHaveExceededWeekLimit(): void
    {
        $weekLimitWithoutCurrentExceededAmount = $this->math->subtract(
            $this->thisWeekOperationsSum,
            self::WEEK_SUM_LIMIT
        );

        // checks if previous transactions did not exceed the limit
        // if they do, we set the sum to WEEK_SUM_LIMIT
        if ((float) $weekLimitWithoutCurrentExceededAmount > 0.00) {
            $this->thisWeekOperationsSum = self::WEEK_SUM_LIMIT;
        }
    }

    /**
     * @throws CurrencyNotAvailableException|ExchangeClientException
     */
    private function calculateHowMuchIsTheLimitExceeded(Operation $operation): string
    {
        $standardCurrencyValue = $operation->getAmount();

        if ($operation->getCurrency() !== StandardCurrency::STANDARD_CURRENCY) {
            $standardCurrencyValue = $this->exchangeClient->exchange(
                $operation->getAmount(),
                $operation->getCurrency(),
                StandardCurrency::STANDARD_CURRENCY
            );
        }

        return $this->math->subtract(
            $this->math->add(
                $this->thisWeekOperationsSum,
                $standardCurrencyValue
            ),
            self::WEEK_SUM_LIMIT
        );
    }

    /**
     * @throws CurrencyNotAvailableException|ExchangeClientException
     */
    private function increaseWeekOperationsSum(Operation $loopOperation): void
    {
        if ($this->isDateBetweenCurrentMondayAndSunday($loopOperation->getDate())) {
            $standardCurrencyAmount = $loopOperation->getAmount();

            // if currency is not equal to the standard one we add weekOperationSum into STANDARD_CURRENCY
            if ($loopOperation->getCurrency() !== StandardCurrency::STANDARD_CURRENCY) {
                $standardCurrencyAmount = $this->exchangeClient->exchange(
                    $loopOperation->getAmount(),
                    $loopOperation->getCurrency(),
                    StandardCurrency::STANDARD_CURRENCY
                );
            }
            $this->thisWeekOperationsSum = $this->math->add($this->thisWeekOperationsSum, $standardCurrencyAmount);
        }
    }

    private function increaseWeekOperationsNumber(Operation $loopOperation): void
    {
        if ($this->isDateBetweenCurrentMondayAndSunday($loopOperation->getDate())) {
            ++$this->thisWeekOperationsNumber;
        }
    }

    private function isDateBetweenCurrentMondayAndSunday(DateTime $date): bool
    {
        return $date >= $this->currentWeekStart && $date <= $this->currentWeekEnd;
    }
}
