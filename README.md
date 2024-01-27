# Flutter-Laravel Integration 🚀

Welcome to the Flutter-Laravel project, where we seamlessly integrate the power of Flutter for the frontend with the robustness of Laravel for the backend! 🌐

## Overview

This project aims to provide a comprehensive and modern solution for building cross-platform mobile applications using Flutter, backed by a powerful Laravel API.

Flutter code for the api connection
```dart
class TaskProvider extends ChangeNotifier {
  final ApiService apiService =
      ApiService('http://192.168.0.115:8000'); // Adjust the URL

  List<Task> tasks = [];

  Future<void> fetchTasks() async {
    tasks = await apiService.fetchTasks();
    notifyListeners();
  }

  Future<void> addTask(Task newTask) async {
    await apiService.addTask(newTask);
    await fetchTasks(); // Refresh the task list after adding a new task
  }

  Future<void> updateTaskCompletion(int id, bool completed) async {
    try {
      await apiService.updateTaskCompletion(id, completed);
      await fetchTasks(); // Refresh the task list after updating task completion
    } catch (e) {
      print('Error updating task completion: $e');
      // Handle the error as needed, for example, show a snackbar or log the error
    }
  }

  Future<void> deleteTask(int id) async {
    // Remove the task from the list
    tasks.removeWhere((task) => task.id == id);

    // Notify listeners after removing the task
    notifyListeners();

    // Perform the API call or other necessary cleanup
    await apiService.deleteTask(id);
  }
}

taskController on Laravel
```php
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

