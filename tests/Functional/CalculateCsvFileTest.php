<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Functional;

use PHPUnit\Framework\TestCase;

/**
 * NOTE: I was not able to check console output from the test level. I am sure that it's possible, but I run out of time and focused on manual testing.
 */
class CalculateCsvFileTest extends TestCase
{
    /**
     * @var string
     */
    private $inputFileLocation;

    /**
     * @var string
     */
    private $outputFileLocation;

    public function setUp()
    {
        $this->inputFileLocation = __DIR__.'/../input/input.csv';
        $this->outputFileLocation = __DIR__.'/../output/output.csv';
    }

    public function testCalculateCsvFile()
    {
        //$this->expectOutputString(file_get_contents($this->outputFileLocation));
        //exec("php script.php $this->inputFileLocation");
    }
}
