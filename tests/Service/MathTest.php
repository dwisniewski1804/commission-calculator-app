<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\CommissionTask\Service\Math;

class MathTest extends TestCase
{
    /**
     * @var Math
     */
    private $math;

    public function setUp()
    {
        $this->math = new Math(2);
    }

    /**
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->add($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            'add 2 natural numbers' => ['1', '2', '3'],
            'add negative number to a positive' => ['-1', '2', '1'],
            'add natural number to a float' => ['1', '1.05123', '2.05'],
        ];
    }

    /**
     * @dataProvider dataProviderForSubtractTesting
     */
    public function testSubtract(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->subtract($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForSubtractTesting(): array
    {
        return [
            'subtract 2 natural numbers' => ['5', '2', '3'],
            'subtract negative number from a positive' => ['1.2', '-2.3', '3.5'],
            'subtract natural number from float' => ['2', '1.05123', '0.94'],
        ];
    }

    /**
     * @dataProvider dataProviderForMultiplyTesting
     */
    public function testMultiply(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->multiply($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForMultiplyTesting(): array
    {
        return [
            'multiply 2 natural numbers' => ['5', '2', '10'],
            'multiply negative number with a positive' => ['1.2', '-2.3', '-2.76'],
            'multiply natural number with float' => ['2', '1.05123', '2.10'],
        ];
    }

    /**
     * @dataProvider dataProviderForDivideTesting
     */
    public function testDivide(string $leftOperand, string $rightOperand, string $expectation)
    {
        $this->assertEquals(
            $expectation,
            $this->math->divide($leftOperand, $rightOperand)
        );
    }

    public function dataProviderForDivideTesting(): array
    {
        return [
            'divide 2 natural numbers' => ['5', '2', '2.5'],
            'divide negative number with a positive' => ['1.2', '-2.3', '-0.52'],
            'divide natural number with float' => ['2', '1.05123', '1.90'],
        ];
    }
}
