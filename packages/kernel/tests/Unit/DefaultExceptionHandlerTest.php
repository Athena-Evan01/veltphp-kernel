<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Velt\Kernel\Exceptions\DefaultExceptionHandler;

final class DefaultExceptionHandlerTest extends TestCase
{
    public function test_it_renders_debug_information(): void
    {
        $handler = new DefaultExceptionHandler(
            true
        );

        $exception = new RuntimeException(
            'Debug error'
        );

        $result = $handler->render(
            $exception
        );

        $this->assertFalse(
            $result['success']
        );

        $this->assertSame(
            RuntimeException::class,
            $result['type']
        );

        $this->assertSame(
            'Debug error',
            $result['message']
        );

        $this->assertArrayHasKey(
            'file',
            $result
        );

        $this->assertArrayHasKey(
            'line',
            $result
        );

        $this->assertArrayHasKey(
            'trace',
            $result
        );
    }

    public function test_it_hides_sensitive_information_in_production(): void
    {
        $handler = new DefaultExceptionHandler(
            false
        );

        $exception = new RuntimeException(
            'Database password leaked'
        );

        $result = $handler->render(
            $exception
        );

        $this->assertFalse(
            $result['success']
        );

        $this->assertSame(
            'An internal error occurred.',
            $result['message']
        );

        $this->assertArrayNotHasKey(
            'trace',
            $result
        );

        $this->assertArrayNotHasKey(
            'file',
            $result
        );

        $this->assertArrayNotHasKey(
            'line',
            $result
        );
    }

    public function test_it_can_report_exception(): void
    {
        $handler = new DefaultExceptionHandler(
            true
        );

        $exception = new Exception(
            'Report test'
        );

        $this->expectNotToPerformAssertions();

        $handler->report($exception);
    }
}