<?php

declare(strict_types=1);

namespace Velt\Kernel;

use Velt\Kernel\Config\ConfigRepository;
use Velt\Kernel\Contracts\ApplicationInterface;
use Velt\Kernel\Contracts\ConfigRepositoryInterface;
use Velt\Kernel\Contracts\ContainerInterface;

final class Application implements ApplicationInterface
{
    public const VERSION = '0.1.0';

    private string $basePath;

    private ContainerInterface $container;

    private ConfigRepositoryInterface $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        string $basePath,
        array $config = []
    ) {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        $this->container = new Container();

        $this->config = new ConfigRepository($config);

        $this->registerBaseBindings();
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }

    public function config(): ConfigRepositoryInterface
    {
        return $this->config;
    }

    public function environment(): string
    {
        return $this->config->get('app.env', 'production');
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

    private function registerBaseBindings(): void
    {
        $this->container->instance('app', $this);

        $this->container->instance('config', $this->config);
    }
}