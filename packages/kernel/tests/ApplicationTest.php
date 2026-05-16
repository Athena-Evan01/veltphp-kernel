<?php

declare(strict_types=1);

namespace Velt\Kernel\Tests;

use PHPUnit\Framework\TestCase;
use Velt\Kernel\Application;
use Velt\Kernel\Config\ConfigRepository;
use Velt\Kernel\Container;
use Velt\Kernel\Contracts\ConfigRepositoryInterface;
use Velt\Kernel\Contracts\ContainerInterface;

final class ApplicationTest extends TestCase
{
    public function test_application_exposes_version(): void
    {
        $this->assertSame(
            '0.1.0',
            Application::VERSION
        );
    }

    public function test_application_can_be_instantiated(): void
    {
        $app = new Application(__DIR__);

        $this->assertInstanceOf(
            Application::class,
            $app
        );
    }

    public function test_application_returns_base_path(): void
    {
        $app = new Application(__DIR__);

        $this->assertSame(
            __DIR__,
            $app->basePath()
        );
    }

    public function test_application_exposes_container(): void
    {
        $app = new Application(__DIR__);

        $this->assertInstanceOf(
            ContainerInterface::class,
            $app->container()
        );
    }

    public function test_application_exposes_config_repository(): void
    {
        $app = new Application(
            __DIR__,
            [
                'app' => [
                    'name' => 'Velt',
                ],
            ]
        );

        $this->assertInstanceOf(
            ConfigRepositoryInterface::class,
            $app->config()
        );

        $this->assertSame(
            'Velt',
            $app->config()->get('app.name')
        );
    }

    public function test_application_detects_local_environment(): void
    {
        $app = new Application(
            __DIR__,
            [
                'app' => [
                    'env' => 'local',
                ],
            ]
        );

        $this->assertTrue($app->isLocal());

        $this->assertFalse($app->isProduction());

        $this->assertFalse($app->isTesting());
    }

    public function test_application_detects_testing_environment(): void
    {
        $app = new Application(
            __DIR__,
            [
                'app' => [
                    'env' => 'testing',
                ],
            ]
        );

        $this->assertTrue($app->isTesting());
    }

    public function test_application_detects_production_environment(): void
    {
        $app = new Application(
            __DIR__,
            [
                'app' => [
                    'env' => 'production',
                ],
            ]
        );

        $this->assertTrue($app->isProduction());
    }

    public function test_application_registers_base_bindings(): void
    {
        $app = new Application(__DIR__);

        $container = $app->container();

        $this->assertSame(
            $app,
            $container->get('app')
        );

        $this->assertInstanceOf(
            ConfigRepository::class,
            $container->get('config')
        );
    }
}