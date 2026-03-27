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

    public function sendMessage(): void
    {
        $prompt = trim($this->input);
        $this->input = '';

        if ($prompt === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $prompt];
        $this->loading = true;

        if ($this->streaming) {
            $this->dispatch('claude-stream-start', prompt: $prompt);
            return;
        }

        $this->queryAndAppendResponse($prompt);
    }

    public function streamComplete(string $content, array $messages): void
    {
        $this->messages = $messages;
        $this->loading = false;
    }

    private function queryAndAppendResponse(string $prompt): void
    {
        $manager = app(ClaudeManager::class);

        $overrides = array_filter([
            'model' => $this->model,
            'system_prompt' => $this->systemPrompt,
            'session_id' => $this->sessionId,
        ]);

        $response = '';

        foreach ($manager->query($prompt, $overrides) as $message) {
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

    public function render()
    {
        return view('claude::livewire.claude-chat');
    }
}
