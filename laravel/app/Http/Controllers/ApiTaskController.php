<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the tasks with filters and search functionality.
     */
    public function index(Request $request)
    {
        $pageSize = 3; // Number of tasks per page
        $user = Auth::user();

        $tasks = Task::where('user_id', $user->id); // Filter by authenticated user

        // Filter tasks based on completion status
        if ($request->has('completed')) {
            $tasks->whereNotNull('completed');
        } elseif ($request->has('pending')) {
            $tasks->whereNull('completed');
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $tasks->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Paginate tasks and return as JSON
        $paginatedTasks = $tasks->orderBy('due_date', 'asc')->paginate($pageSize);

        return response()->json($paginatedTasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $user = Auth::user();

        $task = Task::create(array_merge($validated, ['user_id' => $user->id]));

        return response()->json([
            'message' => 'Task created successfully!',
            'task' => $task
        ], 201); // 201 indicates resource created
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        if ($request->has('completed')) {
            $validated['completed'] = $request->completed ? now() : null;
        }

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully!',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully!']);
    }
}
