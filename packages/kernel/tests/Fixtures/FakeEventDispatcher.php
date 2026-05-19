<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

use Velt\Kernel\Contracts\EventDispatcherInterface;

final class FakeEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string, array<int, callable>>
     */
    private array $listeners = [];

    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(
        object|string $event,
        mixed $payload = null
    ): array {
        return [];
    }
}