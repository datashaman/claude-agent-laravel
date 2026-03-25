## 1. Package Scaffold

- [x] 1.1 Create `composer.json` with package metadata, PHP 8.2+ and Laravel 12+ requirements, `datashaman/claude-agent-sdk` dependency, autoload config, and auto-discovery extra block
- [x] 1.2 Create directory structure: `src/`, `config/`, `routes/`, `resources/views/`, `tests/`
- [x] 1.3 Set up PHPUnit/Pest config with Orchestra Testbench for Laravel package testing

## 2. Service Provider and Facade

- [x] 2.1 Create `config/claude.php` with keys: model, permission_mode, system_prompt, max_turns, allowed_tools, queue, streaming (enabled, route_prefix, middleware)
- [x] 2.2 Create `ClaudeManager` class that wraps the SDK client, configured from the config file
- [x] 2.3 Create `ClaudeServiceProvider` — register singleton binding for `ClaudeManager`, publish config, register commands, conditionally register routes and Livewire component
- [x] 2.4 Create `Claude` facade pointing to `ClaudeManager`
- [x] 2.5 Write tests: provider registration, config publishing, facade resolution, singleton behavior

## 3. Artisan Commands

- [x] 3.1 Create `QueryCommand` (`claude:query`) — accepts prompt argument, --model, --system-prompt, --session options
- [x] 3.2 Create `SessionsListCommand` (`claude:sessions:list`) — table output of sessions
- [x] 3.3 Create `SessionsShowCommand` (`claude:sessions:show {session}`) — display conversation history
- [x] 3.4 Write tests for all three commands (success and error paths)

## 4. Queue Integration

- [x] 4.1 Create `ClaudeQueryCompleted` event with prompt, response, sessionId, timestamp properties
- [x] 4.2 Create `ClaudeQueryFailed` event with prompt, error, exception properties
- [x] 4.3 Create `ClaudeQueryJob` — accepts prompt and overrides, executes query, fires completion/failure events, respects config queue name
- [x] 4.4 Write tests for job dispatch, event firing on success, event firing on failure

## 5. SSE Streaming

- [x] 5.1 Create `ClaudeStreamController` — POST endpoint that streams agent response as SSE events, sends `event: done` on completion
- [x] 5.2 Create `routes/claude.php` with configurable prefix and middleware from config
- [x] 5.3 Conditionally register routes in service provider based on `streaming.enabled` config
- [x] 5.4 Write tests for streaming endpoint, route registration, and disabled streaming

## 6. Livewire Component

- [x] 6.1 Create `ClaudeChat` Livewire component with props for model, system-prompt, session-id
- [x] 6.2 Create Blade view for the chat component (input field, message display, streaming output)
- [x] 6.3 Conditionally register component in service provider (only when Livewire is installed)
- [x] 6.4 Add publishable view tag for customization: `--tag=claude-views`
- [x] 6.5 Write tests for component rendering and graceful skip when Livewire is absent

## 7. Documentation

- [x] 7.1 Create README.md with installation, configuration, usage examples for all features, and requirements (CLI dependency)
- [x] 7.2 Add queue timeout and SSE proxy configuration guidance
