<?php

use App\CommissionTask\Service\CommissionCalculator\BasicCommissionCalculator;
use App\CommissionTask\Service\InputReaderBuilder\InputReaderBuilder;

require_once realpath("vendor/autoload.php");
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$inputPath = $argv[1];
$convertedResult = (new InputReaderBuilder($inputPath))->getReader()->read();
$basicCommissionCalculator = new basicCommissionCalculator();

foreach ($convertedResult as $key => $operation) {
    $commission = $basicCommissionCalculator->calculate($operation, $convertedResult, $key);
    print $commission . "\n";
}

