<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Function to index (list) all tasks
    public function index()
    {
        $tasks = Task::all();
        return view('welcome', ['tasks' => $tasks]);
    }

    // Function to store a new task
    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|unique:tasks,task',
        ]);

        $task = new Task();
        $task->task = $request->input('task');
        $task->save();

        return response()->json(['message' => 'Task added successfully', 'task' => $task]);
    }


    // Function to edit a task
    public function edit(Request $request, $id)
    {
        $request->validate([
            'task' => 'required|string|unique:tasks,task,' . $id,
        ]);

        $task = Task::findOrFail($id);

        if ($task->completed) {
            return response()->json(['message' => 'You cannot edit a completed task', 'task' => $task]);
        }

        $task->task = $request->input('task');
        $task->save();
        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }


    // Function to delete a task
    public function delete($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    // Function to mark a task as completed
    public function complete($id)
    {
        $task = Task::findOrFail($id);
        if($task->completed == false){
            $task->completed = true;
            $task->save();
            return response()->json(['message' => 'Task marked as completed', 'task' => $task,'active' => false]);
        }else{
            $task->completed = false;
            $task->save();
            return response()->json(['message' => 'Task marked as incomplete', 'task' => $task, 'active' => true]);
        }
    }
}
