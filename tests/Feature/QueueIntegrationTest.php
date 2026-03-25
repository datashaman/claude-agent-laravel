<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Tests\Feature;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use DataShaman\Claude\AgentLaravel\Events\ClaudeQueryCompleted;
use DataShaman\Claude\AgentLaravel\Events\ClaudeQueryFailed;
use DataShaman\Claude\AgentLaravel\Jobs\ClaudeQueryJob;
use DataShaman\Claude\AgentLaravel\Tests\TestCase;
use DataShaman\Claude\AgentSdk\Message\Message;
use Generator;
use Illuminate\Support\Facades\Event;
use Mockery;

class QueueIntegrationTest extends TestCase
{
    public function test_job_fires_completed_event_on_success(): void
    {
        Event::fake([ClaudeQueryCompleted::class]);

        $message = new Message(
            type: 'result',
            subtype: null,
            message: null,
            delta: null,
            sessionId: 'sess-1',
            uuid: 'uuid-1',
            parentToolUseId: null,
            result: 'Response text',
            costUsd: 0.01,
            durationMs: 500,
        );

        $generator = (function () use ($message): Generator {
            yield $message;
        })();

        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('query')
            ->once()
            ->with('Test prompt', [])
            ->andReturn($generator);

        $this->app->instance(ClaudeManager::class, $manager);

        $job = new ClaudeQueryJob('Test prompt');
        $job->handle($manager);

        Event::assertDispatched(ClaudeQueryCompleted::class, function ($event) {
            return $event->prompt === 'Test prompt'
                && $event->response === 'Response text'
                && $event->sessionId === 'sess-1';
        });
    }

    public function test_job_fires_failed_event_on_error(): void
    {
        Event::fake([ClaudeQueryFailed::class]);

        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('query')
            ->once()
            ->andThrow(new \RuntimeException('Something went wrong'));

        $this->app->instance(ClaudeManager::class, $manager);

        $job = new ClaudeQueryJob('Test prompt');

        try {
            $job->handle($manager);
        } catch (\RuntimeException) {
            // Expected
        }

        Event::assertDispatched(ClaudeQueryFailed::class, function ($event) {
            return $event->prompt === 'Test prompt'
                && $event->error === 'Something went wrong';
        });
    }

    public function test_job_uses_configured_queue(): void
    {
        config(['claude.queue' => 'ai-queries']);

        $job = new ClaudeQueryJob('Test prompt');

        $this->assertEquals('ai-queries', $job->queue);
    }
}
