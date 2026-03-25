<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Commands;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use DataShaman\Claude\AgentSdk\Exception\SessionNotFoundException;
use Illuminate\Console\Command;

class SessionsShowCommand extends Command
{
    protected $signature = 'claude:sessions:show {session : The session ID to display}';

    protected $description = 'Display conversation history for a Claude session';

    public function handle(ClaudeManager $manager): int
    {
        $sessionId = $this->argument('session');

        try {
            $messages = $manager->getSessionMessages($sessionId);
        } catch (SessionNotFoundException $e) {
            $this->error("Session not found: {$sessionId}");
            return self::FAILURE;
        }

        foreach ($messages as $message) {
            $text = $message->getTextContent();
            if ($text !== null) {
                $role = strtoupper($message->type);
                $this->line("<comment>[{$role}]</comment> {$text}");
            }
        }

        return self::SUCCESS;
    }
}
