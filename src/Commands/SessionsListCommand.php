<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Commands;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use Illuminate\Console\Command;

class SessionsListCommand extends Command
{
    protected $signature = 'claude:sessions:list';

    protected $description = 'List available Claude agent sessions';

    public function handle(ClaudeManager $manager): int
    {
        $sessions = $manager->listSessions();

        if (empty($sessions)) {
            $this->info('No sessions found.');
            return self::SUCCESS;
        }

        $rows = array_map(fn (array $session) => [
            $session['id'] ?? 'N/A',
            $session['created_at'] ?? $session['created'] ?? 'N/A',
        ], $sessions);

        $this->table(['ID', 'Created'], $rows);

        return self::SUCCESS;
    }
}
