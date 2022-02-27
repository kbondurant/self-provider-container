<?php

declare(strict_types=1);

namespace Kbondurant\SelfProviderContainer;

interface SelfProvider
{
    /**
     * @template T
     * @param T $container
     */
    public static function register(mixed $container): void;
}
