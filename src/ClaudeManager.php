<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel;

use DataShaman\Claude\AgentSdk\Claude;
use DataShaman\Claude\AgentSdk\ClaudeAgentClient;
use DataShaman\Claude\AgentSdk\ClaudeAgentOptions;
use DataShaman\Claude\AgentSdk\Enum\PermissionMode;
use DataShaman\Claude\AgentSdk\Message\Message;
use Generator;

class ClaudeManager
{
    public function __construct(
        private readonly array $config,
    ) {}

    public function query(string $prompt, array $overrides = []): Generator
    {
        $options = $this->buildOptions($overrides);

        return Claude::query($prompt, $options);
    }

    public function client(array $overrides = []): ClaudeAgentClient
    {
        $options = $this->buildOptions($overrides);

        return ClaudeAgentClient::create($options);
    }

    /** @return list<array> */
    public function listSessions(): array
    {
        return $this->client()->listSessions();
    }

    /** @return list<Message> */
    public function getSessionMessages(string $sessionId): array
    {
        return $this->client()->getSessionMessages($sessionId);
    }

    public function buildOptions(array $overrides = []): ClaudeAgentOptions
    {
        $options = ClaudeAgentOptions::create();

        $model = $overrides['model'] ?? $this->config['model'] ?? null;
        if ($model) {
            $options = $options->model($model);
        }

        $permissionMode = $overrides['permission_mode'] ?? $this->config['permission_mode'] ?? null;
        if ($permissionMode) {
            $options = $options->permissionMode(PermissionMode::from($permissionMode));
        }

        $systemPrompt = $overrides['system_prompt'] ?? $this->config['system_prompt'] ?? null;
        if ($systemPrompt) {
            $options = $options->systemPrompt($systemPrompt);
        }

        $maxTurns = $overrides['max_turns'] ?? $this->config['max_turns'] ?? 0;
        if ($maxTurns > 0) {
            $options = $options->maxTurns((int) $maxTurns);
        }

        $allowedTools = $overrides['allowed_tools'] ?? $this->config['allowed_tools'] ?? [];
        if (! empty($allowedTools)) {
            $options = $options->allowedTools($allowedTools);
        }

        $sessionId = $overrides['session_id'] ?? null;
        if ($sessionId) {
            $options = $options->sessionId($sessionId);
        }

        return $options;
    }
}
