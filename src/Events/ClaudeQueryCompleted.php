<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClaudeQueryCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $prompt,
        public readonly string $response,
        public readonly ?string $sessionId,
        public readonly \DateTimeImmutable $timestamp,
    ) {}
}
