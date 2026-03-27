<style>
    .claude-chat { display: flex; flex-direction: column; height: 100%; }
    .claude-chat-messages { flex: 1; overflow-y: auto; margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .claude-chat-row { display: flex; }
    .claude-chat-row--user { justify-content: flex-end; }
    .claude-chat-row--assistant { justify-content: flex-start; }
    .claude-chat-bubble { max-width: 75%; padding: 0.5rem 0.75rem; border-radius: 0.5rem; }
    .claude-chat-bubble--user { background: #e2e8f0; color: #1a202c; white-space: pre-wrap; }
    .claude-chat-bubble--assistant { background: #dbeafe; color: #1e3a5f; }
    .claude-chat-bubble--assistant > :first-child { margin-top: 0; }
    .claude-chat-bubble--assistant > :last-child { margin-bottom: 0; }
    .claude-chat-bubble--assistant h1,
    .claude-chat-bubble--assistant h2,
    .claude-chat-bubble--assistant h3,
    .claude-chat-bubble--assistant h4 { margin: 0.75rem 0 0.25rem; font-weight: 600; }
    .claude-chat-bubble--assistant h1 { font-size: 1.25em; }
    .claude-chat-bubble--assistant h2 { font-size: 1.125em; }
    .claude-chat-bubble--assistant h3 { font-size: 1em; }
    .claude-chat-bubble--assistant p { margin: 0.4em 0; }
    .claude-chat-bubble--assistant ul { margin: 0.4em 0; padding-left: 1.5em; list-style-type: disc !important; }
    .claude-chat-bubble--assistant ol { margin: 0.4em 0; padding-left: 1.5em; list-style-type: decimal !important; }
    .claude-chat-bubble--assistant li { margin: 0.15em 0; display: list-item !important; }
    .claude-chat-bubble--assistant blockquote { margin: 0.5em 0; padding: 0.25em 0.75em; border-left: 3px solid rgba(0,0,0,0.2); }
    .claude-chat-bubble--assistant table { border-collapse: collapse; margin: 0.5em 0; }
    .claude-chat-bubble--assistant th,
    .claude-chat-bubble--assistant td { border: 1px solid rgba(0,0,0,0.15); padding: 0.25em 0.5em; }
    .claude-chat-bubble--assistant th { font-weight: 600; }
    .claude-chat-bubble--assistant hr { border: none; border-top: 1px solid rgba(0,0,0,0.15); margin: 0.75em 0; }
    .claude-chat-bubble--assistant pre { background: rgba(0,0,0,0.05); padding: 0.5rem; border-radius: 0.25rem; overflow-x: auto; }
    .claude-chat-bubble--assistant code { font-size: 0.875em; }
    .claude-chat-bubble--assistant pre code { background: none; padding: 0; }
    .claude-chat-bubble--assistant code:not(pre code) { background: rgba(0,0,0,0.05); padding: 0.1em 0.3em; border-radius: 0.2em; }
    .claude-chat-form { display: flex; gap: 0.5rem; }
    .claude-chat-input { flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 0.25rem; }
    .claude-chat-button { padding: 0.5rem 1rem; background: #4a90d9; color: white; border: none; border-radius: 0.25rem; cursor: pointer; }

    @media (prefers-color-scheme: dark) {
        .claude-chat-bubble--user { background: #374151; color: #e5e7eb; }
        .claude-chat-bubble--assistant { background: #1e3a5f; color: #dbeafe; }
        .claude-chat-bubble--assistant pre { background: rgba(0,0,0,0.2); }
        .claude-chat-bubble--assistant code:not(pre code) { background: rgba(0,0,0,0.2); }
        .claude-chat-bubble--assistant blockquote { border-left-color: rgba(255,255,255,0.2); }
        .claude-chat-bubble--assistant th,
        .claude-chat-bubble--assistant td { border-color: rgba(255,255,255,0.15); }
        .claude-chat-bubble--assistant hr { border-top-color: rgba(255,255,255,0.15); }
        .claude-chat-input { background: #374151; color: #e5e7eb; border-color: #4b5563; }
    }

    .dark .claude-chat-bubble--user { background: #374151; color: #e5e7eb; }
    .dark .claude-chat-bubble--assistant { background: #1e3a5f; color: #dbeafe; }
    .dark .claude-chat-bubble--assistant pre { background: rgba(0,0,0,0.2); }
    .dark .claude-chat-bubble--assistant code:not(pre code) { background: rgba(0,0,0,0.2); }
    .dark .claude-chat-bubble--assistant blockquote { border-left-color: rgba(255,255,255,0.2); }
    .dark .claude-chat-bubble--assistant th,
    .dark .claude-chat-bubble--assistant td { border-color: rgba(255,255,255,0.15); }
    .dark .claude-chat-bubble--assistant hr { border-top-color: rgba(255,255,255,0.15); }
    .dark .claude-chat-input { background: #374151; color: #e5e7eb; border-color: #4b5563; }
</style>

<div x-data="claudeChat(@js($streaming))" class="claude-chat">
    <div x-ref="messages" class="claude-chat-messages">
        <template x-for="(message, index) in allMessages" :key="index">
            <div class="claude-chat-row" :class="'claude-chat-row--' + message.role">
                <div
                    class="claude-chat-bubble"
                    :class="'claude-chat-bubble--' + message.role"
                    x-html="message.role === 'assistant' ? renderMarkdown(message.content) : escapeHtml(message.content)"
                ></div>
            </div>
        </template>
    </div>

    <form @submit.prevent="send" class="claude-chat-form">
        <input
            type="text"
            x-model="input"
            placeholder="Type a message..."
            class="claude-chat-input"
            :disabled="loading"
        />
        <button
            type="submit"
            class="claude-chat-button"
            :disabled="loading || !input.trim()"
        >
            Send
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked@15/marked.min.js"></script>

@script
<script>
    Alpine.data('claudeChat', (streaming) => ({
        input: '',
        messages: $wire.messages || [],
        streamingText: null,
        loading: false,

        get allMessages() {
            const msgs = [...this.messages];
            if (this.streamingText !== null) {
                msgs.push({ role: 'assistant', content: this.streamingText || '...' });
            }
            return msgs;
        },

        renderMarkdown(text) {
            if (!text) return '';
            if (typeof marked !== 'undefined') {
                return marked.parse(text, { breaks: true });
            }
            return this.escapeHtml(text);
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        send() {
            const prompt = this.input.trim();
            if (!prompt || this.loading) return;

            this.messages.push({ role: 'user', content: prompt });
            this.input = '';
            this.loading = true;
            this.scrollToBottom();

            if (streaming) {
                this.streamResponse(prompt);
            } else {
                $wire.set('input', prompt);
                $wire.sendMessage().then(() => {
                    this.messages = $wire.messages;
                    this.loading = false;
                    this.scrollToBottom();
                });
            }
        },

        streamResponse(prompt) {
            this.streamingText = '';
            this.scrollToBottom();

            const params = new URLSearchParams();
            params.set('prompt', prompt);
            params.set('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

            if ($wire.model) params.set('model', $wire.model);
            if ($wire.systemPrompt) params.set('system_prompt', $wire.systemPrompt);
            if ($wire.sessionId) params.set('session_id', $wire.sessionId);

            fetch('{{ route("claude.stream") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'text/event-stream',
                },
                body: params.toString(),
            }).then(response => {
                const reader = response.body.getReader();
                const decoder = new TextDecoder();

                const read = () => {
                    reader.read().then(({ done, value }) => {
                        if (done) {
                            this.messages.push({ role: 'assistant', content: this.streamingText });
                            $wire.streamComplete(this.streamingText, this.messages);
                            this.streamingText = null;
                            this.loading = false;
                            this.scrollToBottom();
                            return;
                        }

                        const chunk = decoder.decode(value, { stream: true });
                        for (const line of chunk.split('\n')) {
                            if (line.startsWith('data: ')) {
                                try {
                                    const data = JSON.parse(line.slice(6));
                                    if (data.text) {
                                        this.streamingText += data.text;
                                        this.scrollToBottom();
                                    }
                                } catch (e) {}
                            }
                        }

                        read();
                    });
                };

                read();
            }).catch(() => {
                this.streamingText = null;
                this.loading = false;
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messages;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
    }));
</script>
@endscript
