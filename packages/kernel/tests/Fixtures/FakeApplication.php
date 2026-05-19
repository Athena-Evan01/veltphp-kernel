<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

use Velt\Kernel\Contracts\ApplicationInterface;
use Velt\Kernel\Contracts\ConfigRepositoryInterface;
use Velt\Kernel\Contracts\ContainerInterface;
use Velt\Kernel\Contracts\EnvRepositoryInterface;
use Velt\Kernel\Contracts\EventDispatcherInterface;
use Velt\Kernel\Contracts\ExceptionHandlerInterface;
use Velt\Kernel\Contracts\ServiceProviderInterface;

final class FakeApplication implements ApplicationInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ConfigRepositoryInterface $config,
        private readonly EventDispatcherInterface $events,
        private readonly EnvRepositoryInterface $env,
        private readonly ExceptionHandlerInterface $exceptions,
    ) {
    }

    public function basePath(): string
    {
        return '/velt';
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }

    public function config(): ConfigRepositoryInterface
    {
        return $this->config;
    }

    public function events(): EventDispatcherInterface
    {
        return $this->events;
    }

    public function env(): EnvRepositoryInterface
    {
        return $this->env;
    }

    public function exceptions(): ExceptionHandlerInterface
    {
        return $this->exceptions;
    }

    public function environment(): string
    {
        return 'local';
    }

    public function isLocal(): bool
    {
        return $this->environment() === 'local';
    }

    public function isProduction(): bool
    {
        return $this->environment() === 'production';
    }

    public function isTesting(): bool
    {
        return $this->environment() === 'testing';
    }

    public function isDebug(): bool
    {
        return true;
    }

    public function registerProvider(
        string|ServiceProviderInterface $provider
    ): ServiceProviderInterface {
        if ($provider instanceof ServiceProviderInterface) {
            return $provider;
        }

        return new $provider($this);
    }

    public function boot(): void
    {
    }
}