@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Conversations</h1>
    <div class="list-group">
        @forelse ($conversations as $conversation)
            <a href="{{ route('messages.show', $conversation) }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">
                        @if ($conversation->buyer_id == auth()->id())
                            {{ $conversation->seller->name }}
                        @else
                            {{ $conversation->buyer->name }}
                        @endif
                    </h5>
                    <small>{{ $conversation->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">Conversation about: {{ $conversation->item->title }}</p>
            </a>
        @empty
            <p>You have no conversations.</p>
        @endforelse
    </div>
</div>


@endsection