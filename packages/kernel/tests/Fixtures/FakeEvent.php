<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

final class FakeEvent
{
    public function __construct(
        public readonly string $message
    ) {
    }
}