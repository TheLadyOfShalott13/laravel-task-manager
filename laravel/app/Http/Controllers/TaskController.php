<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     * Function to display all the tasks + accommodating filtering them
     */
    public function index(Request $request)
    {
        $pageSize   = config('constants.PAGE_SIZE');
        $user       = Auth::user(); // Get the authenticated user
        $tasks      = Task::where('user_id', $user->id); // Filter by user

        if ($request->has('status') and $request->status=='completed')
            $tasks->whereNotNull('completed');
        elseif ($request->has('status') and $request->status=='pending')
            $tasks->whereNull('completed');

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
     * @return Factory|View|Application|object
     * Form for creating new task
     */
    public function create()
    {
        return view('tasks.create');
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate(
            //Array for validation params
            [
                'title'         => 'required|string|max:150',
                'description'   => 'nullable|string|max:255',
                'due_date'      => 'required|date',
            ],

            //Array for error messages for every failed validation
            [
                'required'      => 'The :attribute field is required.',
                'date'          => 'The :attribute must be a date.'
            ]
        );

        Auth::user()->tasks()->create($request->only(['title', 'description', 'due_date']));
        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }


    /**
     * @param Task $task
     * @return Factory|View|Application|object
     * Function for displaying the edit form
     */
    public function edit(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            abort(403, 'Unauthorized');
        }
        return view('tasks.edit', ['task' => $task]);
    }


    /**
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     * Function to handle the submission of edit form
     */
    public function update(Request $request, Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate(
            //Array for validation params
            [
                'title'         => 'required|string|max:150',
                'description'   => 'nullable|string|max:255',
                'due_date'      => 'required|date',
            ],

            //Array for error messages for every failed validation
            [
                'required'      => 'The :attribute field is required.',
                'date'          => 'The :attribute must be a date.'
            ]
        );

        $task->update([
            'title'         => $request->title,
            'description'   => $request->description,
            'due_date'      => $request->due_date
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }


    /**
     * @param Task $task
     * @return RedirectResponse
     * Function to delete a task
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
