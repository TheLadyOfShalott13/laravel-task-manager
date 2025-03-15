<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $pageSize = 3;
        $user = Auth::user(); // Get the authenticated user
        $tasks = Task::where('user_id', $user->id); // Filter by user

        if ($request->has('completed')) {
            $tasks->whereNotNull('completed');
        } elseif ($request->has('pending')) {
            $tasks->whereNull('completed');
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $tasks->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        $paginatedTasks = $tasks->orderBy('due_date', 'asc')->paginate($pageSize);

        return view('tasks.index', [
            'tasks' => $paginatedTasks,
            'search' => $request->search
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Auth::user()->tasks()->create($request->only(['title', 'description', 'due_date'])); // Create task for the logged in user.
        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            abort(403, 'Unauthorized');
        }
        return view('tasks.edit', ['task' => $task]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'completed' => $request->has('completed') ? now() : null, // Update the completed timestamp
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            abort(403, 'Unauthorized');
        }
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
