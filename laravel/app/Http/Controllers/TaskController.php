<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller {
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request) {
        $pageSize = 3;
        $tasks = Task::query();

        if ($request->has('completed')) {                                                       // Filter tasks based on completion status
            $tasks->whereNotNull('complete');
        } elseif ($request->has('pending')) {
            $tasks->whereNull('completed');
        }

        if ($request->has('search') && !empty($request->search)) {                              // Search functionality
            $search = $request->search;
            $tasks->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        $paginatedTasks = $tasks->orderBy('due_date', 'asc')->paginate($pageSize);   // Paginate tasks (limit set in $pageSize)


        return view('tasks.index', [                                                           // Pass tasks and search term back to the view
            'tasks' => $paginatedTasks,
            'search' => $request->search
        ]);
    }



    /**
     * Show the form for creating a new task.
     */
    public function create() {
        return view('tasks.create');
    }


    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Task::create($request->only(['title', 'description', 'due_date']));
        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }


    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task) {
        return view('tasks.edit', ['task' => $task]);
    }


    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task) {
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
    public function destroy(Task $task) {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
