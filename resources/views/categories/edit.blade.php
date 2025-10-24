@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ isset($category) ? 'Edit' : 'Add' }} Category</h1>

    <form action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" method="POST">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <button class="btn btn-primary">{{ isset($category) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
