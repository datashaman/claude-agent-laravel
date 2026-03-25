<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel\Facades;

use DataShaman\Claude\AgentLaravel\ClaudeManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Generator query(string $prompt, array $overrides = [])
 * @method static \DataShaman\Claude\AgentSdk\ClaudeAgentClient client(array $overrides = [])
 * @method static list<array> listSessions()
 * @method static list<\DataShaman\Claude\AgentSdk\Message\Message> getSessionMessages(string $sessionId)
 * @method static \DataShaman\Claude\AgentSdk\ClaudeAgentOptions buildOptions(array $overrides = [])
 *
 * @see ClaudeManager
 */
class Claude extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ClaudeManager::class;
    }
}
