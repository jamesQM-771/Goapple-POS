<?php

declare(strict_types=1);

namespace GoApple\Core;

final class StyleEvidence
{
    private string $moduleName;

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    public function buildMessage(string $status): string
    {
        return sprintf(
            '[%s] Module %s validated under PSR-12 style rules.',
            strtoupper($status),
            $this->moduleName
        );
    }
}
