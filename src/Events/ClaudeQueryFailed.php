<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClaudeQueryFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $prompt,
        public readonly string $error,
        public readonly ?\Throwable $exception,
    ) {}
}
