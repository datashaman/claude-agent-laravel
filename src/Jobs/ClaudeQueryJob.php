<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Jobs;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use DataShaman\Claude\AgentLaravel\Events\ClaudeQueryCompleted;
use DataShaman\Claude\AgentLaravel\Events\ClaudeQueryFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClaudeQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $prompt,
        public readonly array $overrides = [],
    ) {
        $queue = config('claude.queue');
        if ($queue) {
            $this->onQueue($queue);
        }
    }

    public function handle(ClaudeManager $manager): void
    {
        try {
            $messages = $manager->query($this->prompt, $this->overrides);

            $response = '';
            $sessionId = null;

            foreach ($messages as $message) {
                $text = $message->getTextContent();
                if ($text !== null) {
                    $response .= $text;
                }
                if ($sessionId === null && $message->sessionId !== '') {
                    $sessionId = $message->sessionId;
                }
            }

            ClaudeQueryCompleted::dispatch(
                $this->prompt,
                $response,
                $sessionId,
                new \DateTimeImmutable,
            );
        } catch (\Throwable $e) {
            ClaudeQueryFailed::dispatch(
                $this->prompt,
                $e->getMessage(),
                $e,
            );

            throw $e;
        }
    }
}
