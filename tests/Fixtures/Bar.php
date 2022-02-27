<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class Bar
{
    public function __construct(
        private string $foo,
    ) {
    }

    public function getFoo(): string
    {
        return $this->foo;
    }
}
