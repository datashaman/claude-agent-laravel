<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Tests\Feature;

use DataShaman\Claude\AgentLaravel\Livewire\ClaudeChat;
use DataShaman\Claude\AgentLaravel\Tests\TestCase;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;

class LivewireTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [
            LivewireServiceProvider::class,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! class_exists(Livewire::class)) {
            $this->markTestSkipped('Livewire is not installed.');
        }
    }

    public function test_component_renders(): void
    {
        Livewire::test(ClaudeChat::class)
            ->assertStatus(200)
            ->assertSee('Send');
    }

    public function test_component_accepts_props(): void
    {
        Livewire::test(ClaudeChat::class, [
            'model' => 'opus',
            'systemPrompt' => 'You are helpful.',
            'sessionId' => 'sess-1',
        ])
            ->assertSet('model', 'opus')
            ->assertSet('systemPrompt', 'You are helpful.')
            ->assertSet('sessionId', 'sess-1');
    }

    public function test_empty_message_is_not_sent(): void
    {
        Livewire::test(ClaudeChat::class)
            ->set('input', '')
            ->call('sendMessage')
            ->assertSet('messages', []);
    }
}
