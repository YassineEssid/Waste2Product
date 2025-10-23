@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Conversation about: {{ $conversation->item->title }}</h1>
        <a href="{{ route('messages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body" id="messages-container" style="height: 400px; overflow-y: scroll;">
            @foreach ($conversation->messages as $message)
                <div class="message-item" data-message-id="{{ $message->id }}">
                    @php
                        $isCurrentUser = $message->sender_id == auth()->id();
                        $bgColor = $isCurrentUser ? '#007bff' : '#f1f1f1';
                        $textColor = $isCurrentUser ? '#fff' : '#000';
                        $alignClass = $isCurrentUser ? 'align-items-end' : 'align-items-start';
                        $timeClass = $isCurrentUser ? 'text-white-50' : 'text-muted';
                    @endphp
                    <div class="d-flex flex-column {{ $alignClass }}">
                        <div class="p-2 rounded mb-2" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                            <p class="mb-0">{{ $message->message }}</p>
                            <small class="{{ $timeClass }}">
                                {{ $message->created_at->format('h:i A') }} | {{ $message->sender->name }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <form id="message-form" method="POST" action="{{ route('messages.store', $conversation) }}">
                @csrf
                <div class="input-group">
                    <input type="text" name="message" id="message-input"
                           class="form-control"
                           placeholder="Type your message..."
                           required
                           autocomplete="off">
                    <button type="submit" class="btn btn-primary" id="send-btn">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-2 text-muted small" id="status-indicator">
        <span class="badge bg-success"><i class="fas fa-circle"></i> Connected</span>
        <span id="last-check"></span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ auth()->id() }};
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const statusIndicator = document.getElementById('status-indicator');
    const lastCheckSpan = document.getElementById('last-check');

    let lastMessageId = {{ $conversation->messages->last()?->id ?? 0 }};
    let isPolling = false;

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Add message to UI
    function addMessage(message) {
        // Check if already exists
        if (document.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }

        const isCurrentUser = message.sender_id === currentUserId;
        const bgColor = isCurrentUser ? '#007bff' : '#f1f1f1';
        const textColor = isCurrentUser ? '#fff' : '#000';
        const alignClass = isCurrentUser ? 'align-items-end' : 'align-items-start';
        const timeClass = isCurrentUser ? 'text-white-50' : 'text-muted';

        const messageHtml = `
            <div class="message-item" data-message-id="${message.id}">
                <div class="d-flex flex-column ${alignClass}">
                    <div class="p-2 rounded mb-2" style="background-color: ${bgColor}; color: ${textColor};">
                        <p class="mb-0">${escapeHtml(message.message)}</p>
                        <small class="${timeClass}">
                            ${message.created_at} | ${escapeHtml(message.sender_name)}
                        </small>
                    </div>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();
        lastMessageId = Math.max(lastMessageId, message.id);
    }

    // Poll for new messages
    async function pollNewMessages() {
        if (isPolling) return;
        isPolling = true;

        try {
            const response = await fetch(`/messages/${conversationId}/poll?since=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                data.messages.forEach(addMessage);

                // Update status
                const now = new Date();
                lastCheckSpan.textContent = `Last check: ${now.toLocaleTimeString()}`;
                statusIndicator.querySelector('.badge').innerHTML = '<i class="fas fa-circle"></i> Connected';
                statusIndicator.querySelector('.badge').className = 'badge bg-success';
            }
        } catch (error) {
            console.error('Polling error:', error);
            statusIndicator.querySelector('.badge').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Reconnecting...';
            statusIndicator.querySelector('.badge').className = 'badge bg-warning';
        } finally {
            isPolling = false;
        }
    }

    // Send message via AJAX
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('message', message);

            const response = await fetch(messageForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.message) {
                    addMessage({
                        id: data.message.id,
                        sender_id: data.message.sender_id,
                        message: data.message.message,
                        created_at: new Date(data.message.created_at).toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }),
                        sender_name: '{{ auth()->user()->name }}'
                    });
                }
                messageInput.value = '';
                messageInput.focus();
            } else {
                alert('Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error('Send error:', error);
            alert('Network error. Please check your connection.');
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
        }
    });

    // Start polling every 2 seconds for real-time updates
    setInterval(pollNewMessages, 2000);

    // Initial scroll
    scrollToBottom();

    // Focus input
    messageInput.focus();
});
</script>
@endsection
