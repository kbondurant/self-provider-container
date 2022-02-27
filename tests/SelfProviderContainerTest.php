<?php

declare(strict_types=1);

namespace Tests;

use Kbondurant\SelfProviderContainer\SelfProviderContainer;
use League\Container\DefinitionContainerInterface;
use League\Container\Exception\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\CustomContainer;
use Tests\Fixtures\Foo;
use Tests\Fixtures\NotSelfProvider;

/**
 * @covers \Kbondurant\SelfProviderContainer\SelfProviderContainer
 */
class SelfProviderContainerTest extends TestCase
{
    /** @var DefinitionContainerInterface & MockObject */
    private DefinitionContainerInterface | MockObject $mockContainer;

    private SelfProviderContainer $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockContainer = $this->createMock(DefinitionContainerInterface::class);

        $this->container = new SelfProviderContainer();
        $this->container->setContainer($this->mockContainer);
    }

    public function test_it_returns_true_if_the_class_implements_the_self_provider_interface(): void
    {
        $this->assertTrue($this->container->has(Foo::class));
    }

    public function test_it_returns_once_it_has_been_registered(): void
    {
        $this->container->get(Foo::class);

        $this->assertFalse($this->container->has(Foo::class));
    }

    public function test_it_returns_false_if_it_is_not_a_self_provider(): void
    {
        $this->assertFalse($this->container->has(NotSelfProvider::class));
    }

    public function test_it_returns_false_if_it_is_not_a_class(): void
    {
        $this->assertFalse($this->container->has('array'));
    }

    public function test_it_throws_an_exception_if_it_is_not_a_self_provider(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Tests\Fixtures\NotSelfProvider is not a self provided service or is already registered');

        $this->container->get(NotSelfProvider::class);
    }

    public function test_it_throws_an_exception_if_it_has_already_been_registered(): void
    {
        $this->container->get(Foo::class);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Tests\Fixtures\Foo is not a self provided service or is already registered');

        $this->container->get(Foo::class);
    }

    public function test_it_passes_the_definition_container_from_league_to_the_register_method(): void
    {
        $this->container->get(Foo::class);

        $this->assertSame($this->mockContainer, Foo::$container);
    }

    public function test_it_passes_the_provided_container_in_the_constructor_to_the_register_method(): void
    {
        $customContainer = new CustomContainer();
        $selfProvidedContainer = new SelfProviderContainer($customContainer);
        $selfProvidedContainer->setContainer($this->mockContainer);

        $selfProvidedContainer->get(Foo::class);

        $this->assertSame($customContainer, Foo::$container);
    }
}
