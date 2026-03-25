## ADDED Requirements

### Requirement: SSE streaming endpoint
The package SHALL provide a `ClaudeStreamController` that accepts a POST request with a prompt and streams the agent response as Server-Sent Events (`text/event-stream`). The route SHALL be registered at `/{route_prefix}/stream` where `route_prefix` is configurable via `config/claude.php`.

#### Scenario: Streaming a response
- **WHEN** a client sends a POST request to `/claude/stream` with `{"prompt": "Explain SOLID principles"}`
- **THEN** the server responds with `Content-Type: text/event-stream` and streams agent output as SSE `data:` events

#### Scenario: Stream completion
- **WHEN** the agent finishes its response
- **THEN** a final SSE event with `event: done` is sent and the connection is closed

#### Scenario: Custom route prefix
- **WHEN** config `claude.streaming.route_prefix` is set to `"ai"`
- **THEN** the streaming endpoint is available at `/ai/stream`

#### Scenario: Middleware configuration
- **WHEN** config `claude.streaming.middleware` is set to `["web", "auth"]`
- **THEN** the streaming route applies those middleware groups

### Requirement: Streaming can be disabled
The streaming routes SHALL only be registered when `config('claude.streaming.enabled')` is `true`.

#### Scenario: Streaming disabled
- **WHEN** config `claude.streaming.enabled` is `false`
- **THEN** no streaming routes are registered and the `/claude/stream` endpoint returns 404

### Requirement: Livewire chat component
The package SHALL provide an optional `ClaudeChat` Livewire component that renders a chat interface and streams agent responses. The component SHALL accept optional props for model, system prompt, and session ID.

#### Scenario: Rendering the component
- **WHEN** a developer includes `<livewire:claude-chat />` in a Blade template
- **THEN** a chat interface is rendered with an input field and message display area

#### Scenario: Sending a message
- **WHEN** a user types a message and submits it in the Livewire component
- **THEN** the message is sent to the agent and the response streams into the chat display in real time

#### Scenario: Livewire not installed
- **WHEN** the `livewire/livewire` package is not installed
- **THEN** the Livewire component is not registered and no errors occur from the service provider
