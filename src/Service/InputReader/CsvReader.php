<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\InputReader;

use App\CommissionTask\Entity\Operation;
use Exception;

/**
 * Converts csv input to array by read() method.
 */
class CsvReader implements InputReaderInterface
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @throws Exception
     */
    public function read(): array
    {
        $result = [];

        if (($handle = fopen($this->path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $result[] = new Operation($data[0], $data[1], $data[2], $data[3], $data[4], $data[5]);
            }
            fclose($handle);
        }

        return $result;
    }
}
