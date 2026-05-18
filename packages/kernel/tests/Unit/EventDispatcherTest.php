<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Velt\Kernel\EventDispatcher;
use Velt\Kernel\Tests\Fixtures\FakeEvent;
use Velt\Kernel\Tests\Fixtures\FakeListener;

final class EventDispatcherTest extends TestCase
{
    public function test_it_calls_listener_when_event_is_dispatched(): void
    {
        $dispatcher = new EventDispatcher();

        $listener = new FakeListener();

        $dispatcher->listen(
            FakeEvent::class,
            $listener
        );

        $dispatcher->dispatch(
            new FakeEvent('hello')
        );

        $this->assertCount(1, $listener->calls);
    }

    public function test_it_calls_multiple_listeners_in_order(): void
    {
        $dispatcher = new EventDispatcher();

        $calls = [];

        $dispatcher->listen(
            'test.event',
            function () use (&$calls): void {
                $calls[] = 'first';
            }
        );

        $dispatcher->listen(
            'test.event',
            function () use (&$calls): void {
                $calls[] = 'second';
            }
        );

        $dispatcher->dispatch('test.event');

        $this->assertSame(
            ['first', 'second'],
            $calls
        );
    }

    public function test_it_returns_listener_results(): void
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->listen(
            'test.event',
            fn () => 'first-result'
        );

        $dispatcher->listen(
            'test.event',
            fn () => 'second-result'
        );

        $results = $dispatcher->dispatch(
            'test.event'
        );

        $this->assertSame(
            ['first-result', 'second-result'],
            $results
        );
    }

    public function test_it_returns_empty_array_when_no_listener_exists(): void
    {
        $dispatcher = new EventDispatcher();

        $results = $dispatcher->dispatch(
            'unknown.event'
        );

        $this->assertSame([], $results);
    }

    public function test_listener_exception_is_not_swallowed(): void
    {
        $dispatcher = new EventDispatcher();

        $dispatcher->listen(
            'test.event',
            function (): void {
                throw new Exception('Listener failed');
            }
        );

        $this->expectException(Exception::class);

        $this->expectExceptionMessage(
            'Listener failed'
        );

        $dispatcher->dispatch('test.event');
    }
}