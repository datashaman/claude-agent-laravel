## ADDED Requirements

### Requirement: claude:query command
The package SHALL provide an Artisan command `claude:query` that sends a prompt to the Claude agent and displays the response. It SHALL accept a prompt as an argument and support `--model`, `--system-prompt`, and `--session` options to override config defaults.

#### Scenario: One-shot query
- **WHEN** a developer runs `php artisan claude:query "Explain dependency injection"`
- **THEN** the command sends the prompt to the Claude agent and outputs the response to the console

#### Scenario: Query with model override
- **WHEN** a developer runs `php artisan claude:query "Hello" --model=opus`
- **THEN** the command uses the specified model instead of the config default

#### Scenario: Query within a session
- **WHEN** a developer runs `php artisan claude:query "Continue from before" --session=abc123`
- **THEN** the command resumes the specified session with the given prompt

### Requirement: claude:sessions:list command
The package SHALL provide an Artisan command `claude:sessions:list` that lists available Claude agent sessions. The output SHALL include session ID and creation timestamp.

#### Scenario: Listing sessions
- **WHEN** a developer runs `php artisan claude:sessions:list`
- **THEN** a table of sessions is displayed with columns for ID and created date

#### Scenario: No sessions exist
- **WHEN** a developer runs `claude:sessions:list` and no sessions exist
- **THEN** a message "No sessions found." is displayed

### Requirement: claude:sessions:show command
The package SHALL provide an Artisan command `claude:sessions:show {session}` that displays the conversation history for a given session ID.

#### Scenario: Showing a session
- **WHEN** a developer runs `php artisan claude:sessions:show abc123`
- **THEN** the conversation messages for session `abc123` are displayed in order

#### Scenario: Invalid session ID
- **WHEN** a developer runs `php artisan claude:sessions:show nonexistent`
- **THEN** an error message "Session not found: nonexistent" is displayed and the command exits with code 1
