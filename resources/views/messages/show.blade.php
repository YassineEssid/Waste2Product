@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Conversation about: {{ $conversation->item->title }}</h1>

    <div class="card">
        <div class="card-body" id="messages-container" style="height: 400px; overflow-y: scroll;">
            @foreach ($conversation->messages as $message)
                <div class="d-flex flex-column {{ $message->user_id == auth()->id() ? 'align-items-end' : 'align-items-start' }}">
                    <div class="p-2 rounded mb-2" style="background-color: #f1f1f1;">
                        <p class="mb-0">{{ $message->body }}</p>
                        <small class="text-muted">{{ $message->created_at->format('h:i A') }} | {{ $message->user->name }}</small>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <form action="{{ route('messages.store', $conversation) }}" method="POST" id="message-form">
                @csrf
                <div class="input-group">
                    <input type="text" name="body" class="form-control" placeholder="Type your message...">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Recharge la page toutes les 3 secondes
    setTimeout(function() {
        window.location.reload();
    }, 10000);
</script>
@endsection

@push('scripts')
<script>
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const authUserId = {{ auth()->id() }};

    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    Echo.private('conversation.{{ $conversation->id }}')
        .listen('.new.message', (e) => {
            const message = e.message;

            const messageElement = document.createElement('div');
            const alignment = message.user.id === authUserId ? 'align-items-end' : 'align-items-start';
            messageElement.className = `d-flex flex-column ${alignment}`;

            const messageBody = `
                <div class="p-2 rounded mb-2" style="background-color: #f1f1f1;">
                    <p class="mb-0">${message.body}</p>
                    <small class="text-muted">${new Date(message.created_at).toLocaleTimeString()} | ${message.user.name}</small>
                </div>
            `;

            messageElement.innerHTML = messageBody;

            messagesContainer.appendChild(messageElement);

            // Scroll to bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const bodyInput = e.target.querySelector('input[name="body"]');
        const body = bodyInput.value;
        bodyInput.value = '';

        axios.post(this.action, { body })
            .catch(error => {
                console.error(error);
                bodyInput.value = body; // Restore message on error
            });
    });
</script>
@endpush