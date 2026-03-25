<?php

return [
    'model' => env('CLAUDE_MODEL', 'sonnet'),
    'permission_mode' => env('CLAUDE_PERMISSION_MODE', 'default'),
    'system_prompt' => env('CLAUDE_SYSTEM_PROMPT', ''),
    'max_turns' => env('CLAUDE_MAX_TURNS', 0),
    'allowed_tools' => [],
    'queue' => env('CLAUDE_QUEUE'),
    'streaming' => [
        'enabled' => true,
        'route_prefix' => 'claude',
        'middleware' => ['web'],
    ],
];
