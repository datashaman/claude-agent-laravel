## ADDED Requirements

### Requirement: Auto-discovered service provider
The package SHALL provide a `ClaudeServiceProvider` that is auto-discovered by Laravel 12+ via the `extra.laravel.providers` key in `composer.json`. No manual registration SHALL be required.

#### Scenario: Package is installed via Composer
- **WHEN** a developer runs `composer require datashaman/claude-agent-laravel`
- **THEN** the `ClaudeServiceProvider` is automatically registered without editing `config/app.php`

### Requirement: Claude facade
The package SHALL provide a `Claude` facade that resolves the `ClaudeManager` from the service container. The facade SHALL be auto-discovered via `extra.laravel.aliases` in `composer.json`.

#### Scenario: Using the facade to send a query
- **WHEN** a developer calls `Claude::query('What is Laravel?')`
- **THEN** the facade resolves the `ClaudeManager` singleton and delegates the call to the SDK client

#### Scenario: Facade is mockable in tests
- **WHEN** a developer calls `Claude::fake()` or `Claude::shouldReceive(...)` in a test
- **THEN** the facade supports standard Laravel facade mocking

### Requirement: Publishable config file
The package SHALL provide a `config/claude.php` configuration file that can be published via `php artisan vendor:publish`. The config SHALL include keys for: `model`, `permission_mode`, `system_prompt`, `max_turns`, `allowed_tools`, and a `streaming` section with `enabled`, `route_prefix`, and `middleware`.

#### Scenario: Publishing the config
- **WHEN** a developer runs `php artisan vendor:publish --tag=claude-config`
- **THEN** a `config/claude.php` file is copied to the application's `config/` directory

#### Scenario: Environment variable overrides
- **WHEN** environment variables `CLAUDE_MODEL`, `CLAUDE_PERMISSION_MODE`, `CLAUDE_SYSTEM_PROMPT`, or `CLAUDE_MAX_TURNS` are set
- **THEN** the corresponding config values SHALL use the environment variable values as defaults

### Requirement: ClaudeManager singleton binding
The service provider SHALL bind a `ClaudeManager` class as a singleton in the service container. The `ClaudeManager` SHALL be configured from `config/claude.php` and SHALL provide methods to interact with the SDK client.

#### Scenario: Resolving from the container
- **WHEN** a developer resolves `ClaudeManager` from the container via dependency injection or `app(ClaudeManager::class)`
- **THEN** the same singleton instance is returned each time, configured with values from `config/claude.php`
