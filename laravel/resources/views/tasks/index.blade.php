@extends('layouts.app')

@section('title', 'Task List')

@section('content')
    <h1 class="mb-4">Task List</h1>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('tasks.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <!-- Create New Task Button -->
    <a href="{{ route('tasks.create') }}" class="btn btn-success mb-3">Create New Task</a>

    @if ($tasks->isEmpty())
        <p class="text-center">No tasks found.</p>
    @else
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Completed</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($tasks as $task)
                <tr id="task-row-{{ $task->id }}">
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->due_date }}</td>
                    <td>
                        @if ($task->completed)
                            <span class="badge bg-success">Completed at {{ $task->completed }}</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="deleteTask({{ $task->id }})">Delete</button>
                        @if (!$task->completed)
                            <button class="btn btn-success btn-sm" onclick="markAsCompleted({{ $task->id }})">Mark as Completed</button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $tasks->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const apiBaseUrl = '{{ url('/api/tasks') }}'; // Base URL for the API

        // Mark a task as completed
        async function markAsCompleted(taskId) {
            try {
                const response = await axios.put(`${apiBaseUrl}/${taskId}`, {
                    completed: true
                });

                alert(response.data.message || 'Task marked as completed!');
                window.location.reload(); // Reload the page to reflect changes
            } catch (error) {
                console.error(error);
                alert('Failed to mark task as completed.');
            }
        }

        // Delete a task
        async function deleteTask(taskId) {
            if (!confirm('Are you sure you want to delete this task?')) return;

            try {
                const response = await axios.delete(`${apiBaseUrl}/${taskId}`);
                alert(response.data.message || 'Task deleted successfully!');
                window.location.reload();
            } catch (error) {
                console.error(error);
                alert('Failed to delete task.');
            }
        }
    </script>
@endsection
