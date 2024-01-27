<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get all tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }

    /**
     * Get a specific task by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task, 200);
    }

    /**
     * Update an existing task.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'completed' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input. ' . $validator->errors()->first()], 400);
        }

        // Update the task
        $task->update([
            'completed' => $request->input('completed', $task->completed),
        ]);

        return response()->json($task, 200);
    }

    /**
     * Create a new task.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input. ' . $validator->errors()->first()], 400);
        }

        // Create a new task
        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'completed' => false, // Default value for a new task
        ]);

        return response()->json($task, 201);
    }

    /**
     * Delete an existing task.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}
