<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

final class FakeListener
{
    /**
     * @var array<int, string>
     */
    public array $calls = [];

    public function __invoke(
        mixed $payload,
        object|string $event
    ): string {
        $this->calls[] = 'called';

        return 'listener-called';
    }
}