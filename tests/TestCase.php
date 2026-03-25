<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Tests;

use DataShaman\Claude\AgentLaravel\ClaudeServiceProvider;
use DataShaman\Claude\AgentLaravel\Facades\Claude;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function getPackageProviders($app): array
    {
        return [
            ClaudeServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Claude' => Claude::class,
        ];
    }
}
