<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Fixtures;

use Throwable;
use Velt\Kernel\Contracts\ExceptionHandlerInterface;

final class FakeExceptionHandler implements ExceptionHandlerInterface
{
    public function report(Throwable $exception): void
    {
        //
    }

    public function render(
        Throwable $exception,
        mixed $context = null
    ): array {
        return [
            'success' => false,
            'message' => $exception->getMessage(),
        ];
    }
}