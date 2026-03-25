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

    public function mount(
        string $model = '',
        string $systemPrompt = '',
        string $sessionId = '',
    ): void {
        $this->model = $model;
        $this->systemPrompt = $systemPrompt;
        $this->sessionId = $sessionId;
    }

    public function sendMessage(): void
    {
        if (trim($this->input) === '') {
            return;
        }

        $prompt = $this->input;
        $this->messages[] = ['role' => 'user', 'content' => $prompt];
        $this->input = '';
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
            $text = $message->getTextContent();
            if ($text !== null) {
                $response .= $text;
            }
            if ($this->sessionId === '' && $message->sessionId !== '') {
                $this->sessionId = $message->sessionId;
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
