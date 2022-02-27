# Self Provider Container

A delegate container for [league/container](https://container.thephpleague.com/).

Declare your definitions directly from your service just before you retrieve it for the first time

## Installation

Via Composer

```bash
composer require kbondurant/self-provider-container
```

## Requirements

The following versions of PHP are supported by this version.

* PHP 8.0
* PHP 8.1

## Usage

You should add the SelfProviderContainer before the ReflectionContainer if you use it too.

```php
use Kbondurant\SelfProviderContainer\SelfProviderContainer;
use League\Container\Container;
use League\Container\ReflectionContainer;

$container = new Container();

$container->delegate(new SelfProviderContainer());
$container->delegate(new ReflectionContainer());
```

Now you can implement the ServiceProvider interface (or any interface that extends it) and add your definitions in the register method.
When you will try to retrieve your LeagueRouter from the container for the first time it will first call the register method before even trying to instantiate it.

```php
class LeagueRouter implements ServiceProvider
{
    public function __construct(
        private Router $router,
    ) {
    }

    /**
     * @param \League\Container\DefinitionContainerInterface $container
     * @return void
     */
    public static function register(mixed $container): void
    {
        $container->add(Router::class, fn () => new Router())
            ->setShared(true);
    }
}
```

By default, an instance of League\Container\DefinitionContainerInterface will be passed to the register::method, but you have the possibility to change that by passing your own container instance to the SelfProviderContainer

```php
use Acme\Container\MyOwnContainer;
use Kbondurant\SelfProviderContainer\SelfProviderContainer;
use League\Container\Container;
use League\Container\ReflectionContainer;

$container = new Container();

$container->delegate(new SelfProviderContainer(new MyOwnContainer()));
$container->delegate(new ReflectionContainer());
```

An instance of Acme\Container\MyOwnContainer will now be passed to the register method

```php
class LeagueRouter implements ServiceProvider
{
    public function __construct(
        private Router $router,
    ) {
    }

    /**
     * @param \Acme\Container\MyOwnContainer $container
     * @return void
     */
    public static function register(mixed $container): void
    {
        $container->singleton(Router::class, fn () => new Router());
    }
}
```

## Limitations

#### You should use this pattern only if the service(s) that you are about to register is injected in one class only (adapters, etc...), otherwise you might redeclare the same service many times which might give you slow performances or even bugs with shared definitions
