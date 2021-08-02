<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Integration;

use DennisKoster\LaravelMaileon\Providers\LaravelMaileonServiceProvider;
use DennisKoster\LaravelMaileon\Tests\mocks\HttpClientMock;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;

abstract class AbstractIntegrationTest extends TestCase
{
    /**
     * @param Application $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelMaileonServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('laravel-maileon.http-client', HttpClientMock::class);
    }
}
