<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

use Velt\Kernel\Contracts\ContainerInterface;

final class FakeContainer implements ContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $services = [];

    /**
     * @var array<string, string>
     */
    private array $aliases = [];

    public function bind(
        string $id,
        callable|string $resolver
    ): void {
        $this->services[$id] = $resolver;
    }

    public function singleton(
        string $id,
        callable|string $resolver
    ): void {
        $this->services[$id] = $resolver;
    }

    public function instance(
        string $id,
        object $instance
    ): void {
        $this->services[$id] = $instance;
    }

    public function alias(
        string $abstract,
        string $alias
    ): void {
        $this->aliases[$alias] = $abstract;
    }

    public function has(string $id): bool
    {
        $id = $this->aliases[$id] ?? $id;

        return isset($this->services[$id]);
    }

    public function get(string $id): mixed
    {
        $id = $this->aliases[$id] ?? $id;

        return $this->services[$id] ?? null;
    }
}