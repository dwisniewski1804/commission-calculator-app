<?php

declare(strict_types=1);

namespace App\CommissionTask\Service\InputReaderBuilder;

use App\CommissionTask\Service\InputReader\InputReaderInterface;
use ReflectionClass;

/**
 * Builds required reader for input file type.
 */
class InputReaderBuilder
{
    /**
     * @var InputReaderInterface
     */
    private $reader;

    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    public function setPath(string $path)
    {
        $this->setReader($path);
    }

    public function getReader(): InputReaderInterface
    {
        return $this->reader;
    }

    private function setReader(string $path)
    {
        $reflectionClass = new ReflectionClass(InputReaderInterface::class);
        $targetClassname = "{$reflectionClass->getNamespaceName()}\\{$this->extractExtension($path)}Reader";

        $this->reader = new $targetClassname($path);
    }

    private function extractExtension(string $path): string
    {
        $pathInfo = pathinfo($path);

        return ucfirst($pathInfo['extension']);
    }
}
