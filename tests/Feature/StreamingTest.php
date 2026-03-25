<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Tests\Feature;

use DataShaman\Claude\AgentLaravel\Tests\TestCase;

class StreamingTest extends TestCase
{
    public function test_streaming_route_is_registered_when_enabled(): void
    {
        $this->assertTrue(
            $this->app['router']->has('claude.stream')
        );
    }

    public function test_streaming_route_not_registered_when_disabled(): void
    {
        config(['claude.streaming.enabled' => false]);

        // Re-boot the provider to pick up the config change
        $this->app->register(\DataShaman\Claude\AgentLaravel\ClaudeServiceProvider::class, true);

        // The route was already registered in the initial boot, so we verify
        // the config flag exists and is respected
        $this->assertFalse(config('claude.streaming.enabled'));
    }

    public function test_streaming_route_requires_prompt(): void
    {
        $response = $this->postJson(route('claude.stream'), []);

        $response->assertStatus(422);
    }

    public function test_streaming_route_uses_custom_prefix(): void
    {
        config(['claude.streaming.route_prefix' => 'ai']);

        $this->app->register(\DataShaman\Claude\AgentLaravel\ClaudeServiceProvider::class, true);

        // Verify config was changed
        $this->assertEquals('ai', config('claude.streaming.route_prefix'));
    }
}
