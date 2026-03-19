<?php

declare(strict_types=1);

namespace Corepine\Support\Tests;

use Corepine\Support\Facades\CorepineColor;
use Corepine\Support\SupportServiceProvider;
use Illuminate\Config\Repository;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SupportServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config): void {
            $config->set('app.debug', true);
            $config->set('app.env', 'testing');
            $config->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        CorepineColor::flush();
    }
}
