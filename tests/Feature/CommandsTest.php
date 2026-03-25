<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Tests\Feature;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use DataShaman\Claude\AgentLaravel\Tests\TestCase;
use DataShaman\Claude\AgentSdk\Exception\SessionNotFoundException;
use DataShaman\Claude\AgentSdk\Message\Message;
use Generator;
use Mockery;

class CommandsTest extends TestCase
{
    public function test_query_command_sends_prompt_and_outputs_response(): void
    {
        $message = new Message(
            type: 'result',
            subtype: null,
            message: null,
            delta: null,
            sessionId: 'sess-1',
            uuid: 'uuid-1',
            parentToolUseId: null,
            result: 'Hello from Claude!',
            costUsd: 0.01,
            durationMs: 500,
        );

        $generator = (function () use ($message): Generator {
            yield $message;
        })();

        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('query')
            ->once()
            ->with('Hello', [])
            ->andReturn($generator);

        $this->app->instance(ClaudeManager::class, $manager);

        $this->artisan('claude:query', ['prompt' => 'Hello'])
            ->expectsOutput('Hello from Claude!')
            ->assertSuccessful();
    }

    public function test_query_command_passes_options(): void
    {
        $generator = (function (): Generator {
            yield from [];
        })();

        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('query')
            ->once()
            ->with('Hi', ['model' => 'opus', 'session_id' => 'sess-1'])
            ->andReturn($generator);

        $this->app->instance(ClaudeManager::class, $manager);

        $this->artisan('claude:query', [
            'prompt' => 'Hi',
            '--model' => 'opus',
            '--session' => 'sess-1',
        ])->assertSuccessful();
    }

    public function test_sessions_list_shows_table(): void
    {
        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('listSessions')
            ->once()
            ->andReturn([
                ['id' => 'sess-1', 'created_at' => '2026-03-25'],
                ['id' => 'sess-2', 'created_at' => '2026-03-24'],
            ]);

        $this->app->instance(ClaudeManager::class, $manager);

        $this->artisan('claude:sessions:list')
            ->expectsTable(['ID', 'Created'], [
                ['sess-1', '2026-03-25'],
                ['sess-2', '2026-03-24'],
            ])
            ->assertSuccessful();
    }

    public function test_sessions_list_shows_message_when_empty(): void
    {
        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('listSessions')
            ->once()
            ->andReturn([]);

        $this->app->instance(ClaudeManager::class, $manager);

        $this->artisan('claude:sessions:list')
            ->expectsOutput('No sessions found.')
            ->assertSuccessful();
    }

    public function test_sessions_show_displays_messages(): void
    {
        $message = new Message(
            type: 'assistant',
            subtype: null,
            message: ['content' => [['type' => 'text', 'text' => 'Hello!']]],
            delta: null,
            sessionId: 'sess-1',
            uuid: 'uuid-1',
            parentToolUseId: null,
            result: null,
            costUsd: null,
            durationMs: null,
        );

        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('getSessionMessages')
            ->once()
            ->with('sess-1')
            ->andReturn([$message]);

        $this->app->instance(ClaudeManager::class, $manager);

        $this->artisan('claude:sessions:show', ['session' => 'sess-1'])
            ->assertSuccessful();
    }

    public function test_sessions_show_errors_on_invalid_session(): void
    {
        $manager = Mockery::mock(ClaudeManager::class);
        $manager->shouldReceive('getSessionMessages')
            ->once()
            ->with('nonexistent')
            ->andThrow(new SessionNotFoundException('nonexistent'));

        $this->app->instance(ClaudeManager::class, $manager);

        $this->artisan('claude:sessions:show', ['session' => 'nonexistent'])
            ->expectsOutput('Session not found: nonexistent')
            ->assertFailed();
    }
}
