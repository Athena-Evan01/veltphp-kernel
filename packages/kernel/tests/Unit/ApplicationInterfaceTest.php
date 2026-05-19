<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Velt\Kernel\Contracts\ApplicationInterface;
use Velt\Kernel\Tests\Fixtures\FakeApplication;
use Velt\Kernel\Tests\Fixtures\FakeConfigRepository;
use Velt\Kernel\Tests\Fixtures\FakeContainer;
use Velt\Kernel\Tests\Fixtures\FakeEnvRepository;
use Velt\Kernel\Tests\Fixtures\FakeEventDispatcher;
use Velt\Kernel\Tests\Fixtures\FakeExceptionHandler;

final class ApplicationInterfaceTest extends TestCase
{
    public function test_fake_implements_application_contract(): void
    {
        $app = $this->createApplication();

        $this->assertInstanceOf(
            ApplicationInterface::class,
            $app
        );
    }

    public function test_it_returns_base_path(): void
    {
        $app = $this->createApplication();

        $this->assertSame(
            '/velt',
            $app->basePath()
        );
    }

    public function test_it_returns_container_instance(): void
    {
        $container = new FakeContainer();

        $app = new FakeApplication(
            $container,
            new FakeConfigRepository(),
            new FakeEventDispatcher(),
            new FakeEnvRepository(),
            new FakeExceptionHandler(),
        );

        $this->assertSame(
            $container,
            $app->container()
        );
    }

    public function test_it_returns_config_repository_instance(): void
    {
        $config = new FakeConfigRepository();

        $app = new FakeApplication(
            new FakeContainer(),
            $config,
            new FakeEventDispatcher(),
            new FakeEnvRepository(),
            new FakeExceptionHandler(),
        );

        $this->assertSame(
            $config,
            $app->config()
        );
    }

    public function test_it_returns_environment(): void
    {
        $app = $this->createApplication();

        $this->assertSame(
            'local',
            $app->environment()
        );
    }

    public function test_it_detects_local_environment(): void
    {
        $app = $this->createApplication();

        $this->assertTrue(
            $app->isLocal()
        );
    }

    public function test_it_detects_non_production_environment(): void
    {
        $app = $this->createApplication();

        $this->assertFalse(
            $app->isProduction()
        );
    }

    public function test_it_detects_non_testing_environment(): void
    {
        $app = $this->createApplication();

        $this->assertFalse(
            $app->isTesting()
        );
    }

    private function createApplication(): FakeApplication
    {
        return new FakeApplication(
            new FakeContainer(),
            new FakeConfigRepository(),
            new FakeEventDispatcher(),
            new FakeEnvRepository(),
            new FakeExceptionHandler(),
        );
    }
}