<div>
    <div class="claude-chat-messages" style="max-height: 500px; overflow-y: auto; margin-bottom: 1rem;">
        @foreach($messages as $message)
            <div class="claude-chat-message claude-chat-message--{{ $message['role'] }}" style="margin-bottom: 0.5rem; padding: 0.5rem; border-radius: 0.25rem; {{ $message['role'] === 'user' ? 'background: #f0f0f0;' : 'background: #e8f4f8;' }}">
                <strong>{{ ucfirst($message['role']) }}:</strong>
                <div>{!! nl2br(e($message['content'])) !!}</div>
            </div>
        @endforeach

        @if($loading)
            <div class="claude-chat-loading" style="padding: 0.5rem; color: #666;">
                Thinking...
            </div>
        @endif
    </div>

    <form wire:submit="sendMessage" style="display: flex; gap: 0.5rem;">
        <input
            type="text"
            wire:model="input"
            placeholder="Type a message..."
            style="flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 0.25rem;"
            @if($loading) disabled @endif
        />
        <button
            type="submit"
            style="padding: 0.5rem 1rem; background: #4a90d9; color: white; border: none; border-radius: 0.25rem; cursor: pointer;"
            @if($loading) disabled @endif
        >
            Send
        </button>
    </form>
</div>
