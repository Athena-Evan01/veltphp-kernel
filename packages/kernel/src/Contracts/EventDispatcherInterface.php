<?php

declare(strict_types=1);

namespace Velt\Kernel\Contracts;

/**
 * Représente un dispatcher d'événements synchrone.
 */
interface EventDispatcherInterface
{
    /**
     * Enregistre un listener pour un événement.
     */
    public function listen(
        string $event,
        callable $listener
    ): void;

    /**
     * Déclenche un événement.
     *
     * @return array<int, mixed>
     */
    public function dispatch(
        object|string $event,
        mixed $payload = null
    ): array;
}