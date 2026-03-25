<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Tests\Feature;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use DataShaman\Claude\AgentLaravel\ClaudeServiceProvider;
use DataShaman\Claude\AgentLaravel\Facades\Claude;
use DataShaman\Claude\AgentLaravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_provider_is_registered(): void
    {
        $this->assertArrayHasKey(
            ClaudeServiceProvider::class,
            $this->app->getLoadedProviders()
        );
    }

    public function test_config_is_merged(): void
    {
        $this->assertNotNull(config('claude.model'));
        $this->assertEquals('sonnet', config('claude.model'));
        $this->assertEquals('default', config('claude.permission_mode'));
    }

    public function test_claude_manager_is_singleton(): void
    {
        $manager1 = $this->app->make(ClaudeManager::class);
        $manager2 = $this->app->make(ClaudeManager::class);

        $this->assertSame($manager1, $manager2);
    }

    public function test_claude_manager_is_instance_of_claude_manager(): void
    {
        $manager = $this->app->make(ClaudeManager::class);

        $this->assertInstanceOf(ClaudeManager::class, $manager);
    }

    public function test_facade_resolves_to_manager(): void
    {
        $resolved = Claude::getFacadeRoot();

        $this->assertInstanceOf(ClaudeManager::class, $resolved);
    }

    public function test_config_respects_env_overrides(): void
    {
        config(['claude.model' => 'opus']);

        $manager = $this->app->make(ClaudeManager::class);
        $options = $manager->buildOptions();

        $this->assertEquals('opus', $options->model);
    }
}
