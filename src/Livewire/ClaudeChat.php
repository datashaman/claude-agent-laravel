<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Livewire;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use Livewire\Component;

class ClaudeChat extends Component
{
    public string $model = '';

    public string $systemPrompt = '';

    public string $sessionId = '';

    public string $input = '';

    public array $messages = [];

    public bool $loading = false;

    public bool $streaming = true;

    public function mount(
        string $model = '',
        string $systemPrompt = '',
        string $sessionId = '',
        ?bool $streaming = null,
    ): void {
        $this->model = $model;
        $this->systemPrompt = $systemPrompt;
        $this->sessionId = $sessionId;
        $this->streaming = $streaming ?? config('claude.streaming.enabled', true);
    }

    public function sendMessage(string $prompt): void
    {
        if (trim($prompt) === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $prompt];
        $this->loading = true;

        $manager = app(ClaudeManager::class);

        $overrides = array_filter([
            'model' => $this->model,
            'system_prompt' => $this->systemPrompt,
            'session_id' => $this->sessionId,
        ]);

        $response = '';
        $messages = $manager->query($prompt, $overrides);

        foreach ($messages as $message) {
            if ($this->sessionId === '' && $message->sessionId !== '') {
                $this->sessionId = $message->sessionId;
            }
            if ($message->type === 'assistant') {
                $text = $message->getTextContent();
                if ($text !== null) {
                    $response .= $text;
                }
            }
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $response];
        $this->loading = false;
    }

    public function streamComplete(string $ignored, array $messages): void
    {
        $this->messages = $messages;
        $this->loading = false;
    }

    public function render()
    {
        return view('claude::livewire.claude-chat');
    }
}
