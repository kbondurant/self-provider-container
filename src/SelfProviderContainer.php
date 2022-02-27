<?php

declare(strict_types=1);

namespace Kbondurant\SelfProviderContainer;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;

class SelfProviderContainer implements ContainerAwareInterface, ContainerInterface
{
    use ContainerAwareTrait;

    /**
     * @var SelfProvider[] | class-string[]
     */
    private array $registeredProviders = [];

    public function __construct(
        protected mixed $registerContainer = null,
    ) {
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('%s is not a self provided service or is already registered', $id));
        }

        /** @var SelfProvider | class-string $id */
        $id::register($this->registerContainer ?? $this->getContainer());

        $this->registeredProviders[] = $id;

        /** @var string $id */
        return $this->getContainer()->get($id);
    }

    public function has(string $id): bool
    {
        if (!class_exists($id) or $this->isRegistered($id)) {
            return false;
        }

        $interfaces = class_implements($id);
        if ($interfaces === false or count($interfaces) === 0) {
            return false;
        }

        return in_array(SelfProvider::class, $interfaces, true);
    }

    private function isRegistered(string $id): bool
    {
        return in_array($id, $this->registeredProviders, true);
    }
}
