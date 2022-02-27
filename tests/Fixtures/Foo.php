<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Kbondurant\SelfProviderContainer\SelfProvider;

class Foo implements SelfProvider
{
    public static mixed $container;

    public function __construct(
        private Bar $bar,
    ) {
    }

    public static function register(mixed $container): void
    {
        self::$container = $container;

        $container->add(Bar::class, fn () => new Bar('toto'));
    }

    public function getBar(): Bar
    {
        return $this->bar;
    }
}
