<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Velt\Kernel\Application;
use Velt\Kernel\Tests\Fixtures\FakeServiceProvider;

final class ApplicationEventsTest extends TestCase
{
    public function test_it_dispatches_provider_registered_event(): void
    {
        $app = new Application(__DIR__);

        $called = false;

        $app->events()->listen(
            'provider.registered',
            function () use (&$called): void {
                $called = true;
            }
        );

        $app->registerProvider(
            FakeServiceProvider::class
        );

        $this->assertTrue($called);
    }

    public function test_it_dispatches_application_booted_event(): void
    {
        $app = new Application(__DIR__);

        $called = false;

        $app->events()->listen(
            'application.booted',
            function () use (&$called): void {
                $called = true;
            }
        );

        $app->boot();

        $this->assertTrue($called);
    }
}