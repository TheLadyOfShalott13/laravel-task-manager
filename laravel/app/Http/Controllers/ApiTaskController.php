<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTaskController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * Function generate token for APIs
     */
    public function generate_token(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;
            return response()->json(
                [
                'message'   => 'Login successful',
                'token'     => $token,
                ],
                200)->send();
        }

        return response()->json(['message' => 'Invalid credentials'], 401)->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Function to fetch all tasks
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)->get(); //Query to get all tasks with matching user id
        if (count($tasks) == 0) {
            $find_user = User::where('id', $request->user()->id)->first();
            if (count($find_user)==0)
                return response()->json([ 'error' => 'User not found' ], 404);
        }
        return response()->json([ 'tasks' => $tasks ], 200)->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Function to create a task
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
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

        try{
            $task = Task::create(array_merge($validated, ['user_id' => $request->user()->id]));
            return response()->json(
                [
                    'message'   => 'Task created successfully!',
                    'task'      => $task
                ],
                201         //Code for resource creation
            );
        }
        catch (\Exception $exception){
            return response()->json(
                [
                    'message'   => 'Error while creating task!',
                    'task'      => $task
                ],
                400         //Code for error in resource creation
            );
        }
    }


    /**
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     * API to fetch a single task on basis of user id and task id
     */
    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You are not authorized to view this task'], 403);
        }

        $single_task = Task::where('user_id', $request->user()->id)
                        ->where('id', $task->id)
                        ->first();

        return response()->json([ 'single_task' => $single_task ], 200)->send();
    }


    /**
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     * Function to update a single task
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate(
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

        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'You are not authorized to update this task'], 403);
        }

        if ($task->update($validated))
            return response()->json(['message' => 'Task updated successfully'], 200);
        else
            return response()->json(['message' => 'Task not found'], 404);
    }


    /**
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     * Function to delete a task
     */
    public function destroy(Request $request, Task $task)
    {
        if ($request->user()->id !== $task->user_id) {
            return response()->json(['message' => 'You are not authorized to delete this task.'], 403);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully!']);
    }
}
