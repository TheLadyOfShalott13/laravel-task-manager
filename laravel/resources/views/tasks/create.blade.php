@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
    <h1 class="mb-4">Create a New Task</h1>

    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="mb-3">
            <label
                for="title"
                class="form-label"
            >
                Task title
            </label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-control"
                maxlength="100"
                required
            >
        </div>

        <div class="mb-3">
            <label
                for="description"
                class="form-label"
            >
                Task Description
            </label>
            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="3"
            >
            </textarea>
        </div>

        <div class="mb-3">
            <label
                for="due_date"
                class="form-label"
            >
                Task Due Date
            </label>
            <input
                type="date"
                id="due_date"
                name="due_date"
                class="form-control"
                required
                value="{{ old('due_date') }}"
            >
        </div>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Create Task
        </button>
        <a
            href="{{ route('tasks.index') }}"
            class="btn btn-secondary"
        >
            Cancel
        </a>
    </form>
@endsection
