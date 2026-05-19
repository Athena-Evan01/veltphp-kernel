<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

use Velt\Kernel\Contracts\EnvRepositoryInterface;

final class FakeEnvRepository implements EnvRepositoryInterface
{
    public function load(string $path): void
    {
    }

    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $default;
    }

    public function has(string $key): bool
    {
        return false;
    }

    public function set(
        string $key,
        mixed $value
    ): void {
    }
}