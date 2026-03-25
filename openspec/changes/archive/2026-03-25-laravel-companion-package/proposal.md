## Why

Laravel is the dominant PHP framework and the primary target for `datashaman/claude-agent-sdk`. Developers need a native Laravel integration that follows framework conventions — service container bindings, config files, Artisan commands, queued jobs, and real-time streaming — rather than manually wiring the SDK into every project. This package eliminates boilerplate and makes Claude agent capabilities feel like a first-class Laravel feature.

## What Changes

- New Composer package `datashaman/claude-agent-laravel` providing a Laravel service provider, facade, configuration, Artisan commands, queue jobs, and streaming support.
- Registers `Claude` facade and publishes `config/claude.php` for model, permission mode, system prompt, and other SDK defaults.
- Adds Artisan commands: `claude:query` (interactive/one-shot queries), `claude:sessions:list`, `claude:sessions:show`.
- Provides a `ClaudeQueryJob` for dispatching agent queries via Laravel's queue system, with result broadcasting via events.
- Implements SSE streaming endpoint and an optional Livewire component for real-time agent response streaming to the browser.

## Capabilities

### New Capabilities
- `service-provider`: Auto-discovered service provider, `Claude` facade, and `config/claude.php` with SDK defaults (model, permission mode, system prompt, max turns, allowed tools).
- `artisan-commands`: CLI commands `claude:query`, `claude:sessions:list`, `claude:sessions:show` for interacting with the agent from the terminal.
- `queue-integration`: `ClaudeQueryJob` dispatched via Laravel queues, with `ClaudeQueryCompleted` / `ClaudeQueryFailed` events for result handling.
- `streaming`: SSE controller endpoint and optional Livewire component for streaming agent responses to the browser in real time.

### Modified Capabilities

_(none — this is a new package)_

## Impact

- **Dependencies**: Requires PHP 8.2+, Laravel 12+, `datashaman/claude-agent-sdk`.
- **New files**: Service provider, facade, config, Artisan commands, job, events, SSE controller, Livewire component, route file.
- **APIs**: Adds a `/claude/stream` route (configurable prefix) for SSE streaming.
- **Testing**: Package will include feature tests using Orchestra Testbench.
