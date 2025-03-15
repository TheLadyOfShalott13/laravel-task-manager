@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
    <h1 class="mb-4">Edit Task</h1>

    <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" id="title" name="title" class="form-control" required value="{{ old('title', $task->title) }}">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ old('due_date', $task->due_date) }}">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" id="completed" name="completed" class="form-check-input" value="1" @if ($task->completed) checked @endif>
            <label for="completed" class="form-check-label">Mark as Completed</label>
        </div>

        <button type="submit" class="btn btn-primary">Update Task</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
