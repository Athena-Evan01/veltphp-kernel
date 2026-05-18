<?php

declare(strict_types=1);

namespace Velt\Kernel;

use Velt\Kernel\Contracts\EventDispatcherInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * Liste des listeners enregistrés.
     *
     * @var array<string, array<int, callable>>
     */
    private array $listeners = [];

    public function listen(
        string $event,
        callable $listener
    ): void {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(
        object|string $event,
        mixed $payload = null
    ): array {
        $eventName = is_object($event)
            ? $event::class
            : $event;

        $results = [];

        if (! isset($this->listeners[$eventName])) {
            return $results;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            $results[] = $listener($payload, $event);
        }

        return $results;
    }
}