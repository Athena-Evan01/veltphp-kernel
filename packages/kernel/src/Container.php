<?php

declare(strict_types=1);

namespace Velt\Kernel;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Velt\Kernel\Contracts\ContainerInterface;
use Velt\Kernel\Exceptions\ContainerResolutionException;
use Velt\Kernel\Exceptions\ServiceNotFoundException;

final class Container implements ContainerInterface
{
    /**
     * Services enregistrés.
     *
     * @var array<string, mixed>
     */
    private array $bindings = [];

    /**
     * Instances singleton résolues.
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Services marqués comme singleton.
     *
     * @var array<string, bool>
     */
    private array $singletons = [];

    /**
     * Aliases de services.
     *
     * @var array<string, string>
     */
    private array $aliases = [];

    public function bind(
        string $id,
        callable|string $resolver
    ): void {
        $this->bindings[$id] = $resolver;
    }

    public function singleton(
        string $id,
        callable|string $resolver
    ): void {
        $this->singletons[$id] = true;

        $this->bindings[$id] = $resolver;
    }

    public function instance(
        string $id,
        object $instance
    ): void {
        $this->instances[$id] = $instance;

        $this->bindings[$id] = $instance;

        $this->singletons[$id] = true;
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

        if (
            isset($this->bindings[$id]) ||
            isset($this->instances[$id])
        ) {
            return true;
        }

        return class_exists($id);
    }

    public function get(string $id): mixed
    {
        $id = $this->aliases[$id] ?? $id;

        /**
         * Instance singleton déjà résolue.
         */
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        /**
         * Service explicitement bindé.
         */
        if (isset($this->bindings[$id])) {
            $service = $this->resolve(
                $this->bindings[$id]
            );

            if (isset($this->singletons[$id])) {
                $this->instances[$id] = $service;
            }

            return $service;
        }

        /**
         * Autowiring classe concrète.
         */
        if (class_exists($id)) {
            return $this->build($id);
        }

        throw new ServiceNotFoundException(
            "Service not found: {$id}"
        );
    }

    private function resolve(
        callable|string $resolver
    ): mixed {
        if (is_callable($resolver)) {
            return $resolver($this);
        }

        if (is_string($resolver)) {
            if (class_exists($resolver)) {
                return $this->build($resolver);
            }

            throw new ContainerResolutionException(
                "Unable to resolve service [{$resolver}]."
            );
        }

        return $resolver;
    }

    private function build(string $class): object
    {
        $reflection = new ReflectionClass($class);

        if (! $reflection->isInstantiable()) {
            throw new ContainerResolutionException(
                "Class {$class} is not instantiable."
            );
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];

        foreach (
            $constructor->getParameters()
            as $parameter
        ) {
            $dependencies[] = $this->resolveParameter(
                $class,
                $parameter
            );
        }

        return $reflection->newInstanceArgs(
            $dependencies
        );
    }

    private function resolveParameter(
        string $class,
        ReflectionParameter $parameter
    ): mixed {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType) {
            throw new ContainerResolutionException(
                "Unable to resolve parameter \${$parameter->getName()} in {$class}"
            );
        }

        if ($type->isBuiltin()) {
            throw new ContainerResolutionException(
                "Cannot resolve scalar parameter \${$parameter->getName()} in {$class}"
            );
        }

        return $this->get(
            $type->getName()
        );
    }
}