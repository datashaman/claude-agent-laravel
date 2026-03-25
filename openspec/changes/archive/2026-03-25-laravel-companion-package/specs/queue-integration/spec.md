## ADDED Requirements

### Requirement: ClaudeQueryJob
The package SHALL provide a `ClaudeQueryJob` that can be dispatched to a Laravel queue. The job SHALL accept a prompt string and optional overrides (model, system prompt, session ID). Upon completion, it SHALL fire a `ClaudeQueryCompleted` event with the response.

#### Scenario: Dispatching a query job
- **WHEN** a developer dispatches `ClaudeQueryJob::dispatch('Summarize this document')`
- **THEN** the job is queued and executes the query via the SDK when processed

#### Scenario: Job completes successfully
- **WHEN** the `ClaudeQueryJob` finishes processing and receives a response from the agent
- **THEN** a `ClaudeQueryCompleted` event is fired containing the prompt, response, session ID, and any metadata

#### Scenario: Job fails
- **WHEN** the `ClaudeQueryJob` encounters an error during execution
- **THEN** a `ClaudeQueryFailed` event is fired containing the prompt, error message, and exception details

### Requirement: ClaudeQueryCompleted event
The package SHALL provide a `ClaudeQueryCompleted` event class that contains the query prompt, the agent response, the session ID, and a timestamp. The event SHALL implement `ShouldBroadcast` optionally (configurable).

#### Scenario: Listening for completed queries
- **WHEN** a developer registers a listener for `ClaudeQueryCompleted`
- **THEN** the listener receives the event with `$event->prompt`, `$event->response`, `$event->sessionId`, and `$event->timestamp`

### Requirement: ClaudeQueryFailed event
The package SHALL provide a `ClaudeQueryFailed` event class that contains the query prompt, the error message, and the exception. The event SHALL implement `ShouldBroadcast` optionally (configurable).

#### Scenario: Listening for failed queries
- **WHEN** a developer registers a listener for `ClaudeQueryFailed`
- **THEN** the listener receives the event with `$event->prompt`, `$event->error`, and `$event->exception`

### Requirement: Configurable queue name
The `ClaudeQueryJob` SHALL use the default queue unless the developer specifies a queue name via `ClaudeQueryJob::dispatch($prompt)->onQueue('claude')` or via a `queue` key in `config/claude.php`.

#### Scenario: Custom queue
- **WHEN** config `claude.queue` is set to `"ai-queries"`
- **THEN** `ClaudeQueryJob` dispatches to the `ai-queries` queue by default
