## Context

This is a greenfield Laravel package (`datashaman/claude-agent-laravel`) that wraps `datashaman/claude-agent-sdk` — a PHP SDK for interacting with the Claude CLI agent. The SDK handles process management, session persistence, and message parsing. This package provides Laravel-native bindings: service provider, facade, config, Artisan commands, queued jobs, and real-time streaming.

Target: PHP 8.2+, Laravel 12+.

## Goals / Non-Goals

**Goals:**
- Provide a zero-config service provider with auto-discovery that binds SDK classes into the container
- Expose a `Claude` facade for convenient access to the SDK client
- Publish a `config/claude.php` with sensible defaults (model, permission mode, system prompt, max turns, allowed tools)
- Ship Artisan commands for CLI interaction with Claude sessions
- Enable background agent queries via Laravel's queue system with event-driven results
- Support real-time streaming of agent responses via SSE and an optional Livewire component

**Non-Goals:**
- Direct HTTP API client for the Anthropic REST API (that's a different concern from CLI agent interaction)
- Authentication/authorization middleware (users integrate with their own auth)
- Frontend JavaScript framework beyond Livewire integration
- Multi-tenant agent isolation (single config per application)

## Decisions

### 1. Service container bindings via singleton
The SDK client will be bound as a singleton in the container, configured from `config/claude.php`. This avoids spawning multiple CLI processes with different configs.

**Alternative**: Bind as non-shared — rejected because the client is stateless configuration and process spawning; singleton is appropriate.

### 2. Facade over helper function
Provide a `Claude` facade rather than a global helper. Facades are the Laravel convention for package APIs, they're mockable in tests, and they don't pollute the global namespace.

### 3. Queue job with events pattern
`ClaudeQueryJob` dispatches the query synchronously within the job, then fires `ClaudeQueryCompleted` or `ClaudeQueryFailed` events. This follows Laravel's job/event pattern and lets consumers listen for results however they choose (notifications, broadcasting, database writes).

**Alternative**: Return a Promise/deferred — rejected as non-idiomatic in Laravel's queue system.

### 4. SSE streaming via dedicated controller
A `ClaudeStreamController` handles SSE by reading the SDK's streaming output and flushing it as `text/event-stream` chunks. This is a simple controller route, not tied to Livewire, so it works with any frontend.

The optional Livewire component consumes this SSE endpoint or uses Livewire's own wire:stream mechanism.

### 5. Package structure
Standard Laravel package layout:
```
src/
  ClaudeServiceProvider.php
  Facades/Claude.php
  ClaudeManager.php
  Commands/
    QueryCommand.php
    SessionsListCommand.php
    SessionsShowCommand.php
  Jobs/ClaudeQueryJob.php
  Events/
    ClaudeQueryCompleted.php
    ClaudeQueryFailed.php
  Http/Controllers/ClaudeStreamController.php
  Livewire/ClaudeChat.php
config/claude.php
routes/claude.php
```

### 6. Config structure
```php
return [
    'model' => env('CLAUDE_MODEL', 'sonnet'),
    'permission_mode' => env('CLAUDE_PERMISSION_MODE', 'default'),
    'system_prompt' => env('CLAUDE_SYSTEM_PROMPT', ''),
    'max_turns' => env('CLAUDE_MAX_TURNS', 0),
    'allowed_tools' => [],
    'streaming' => [
        'enabled' => true,
        'route_prefix' => 'claude',
        'middleware' => ['web'],
    ],
];
```

## Risks / Trade-offs

- **[CLI dependency]** The SDK requires the Claude CLI to be installed on the server. → Document this clearly in README; provide a health-check command or test helper.
- **[Long-running queue jobs]** Agent queries can take significant time. → Document queue timeout configuration; recommend dedicated queue for Claude jobs.
- **[SSE connection limits]** Long-lived SSE connections consume server resources. → Document nginx/Apache proxy buffering config; recommend queue-based approach for high concurrency.
- **[Livewire version coupling]** Livewire 3 API may change. → Keep Livewire component optional; suggest as a separate composer dependency or feature flag.
