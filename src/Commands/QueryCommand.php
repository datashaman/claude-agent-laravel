<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Commands;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use Illuminate\Console\Command;

class QueryCommand extends Command
{
    protected $signature = 'claude:query
        {prompt : The prompt to send to Claude}
        {--model= : Override the model}
        {--system-prompt= : Override the system prompt}
        {--session= : Resume a session by ID}';

    protected $description = 'Send a query to the Claude agent';

    public function handle(ClaudeManager $manager): int
    {
        $overrides = array_filter([
            'model' => $this->option('model'),
            'system_prompt' => $this->option('system-prompt'),
            'session_id' => $this->option('session'),
        ]);

        $messages = $manager->query($this->argument('prompt'), $overrides);

        foreach ($messages as $message) {
            $text = $message->getTextContent();
            if ($text !== null) {
                $this->line($text);
            }
        }

        return self::SUCCESS;
    }
}
