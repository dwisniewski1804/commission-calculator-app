<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\InputReader;

/**
 * Describes reader functionality for any input type.
 */
interface InputReaderInterface
{
    public function read();
}
