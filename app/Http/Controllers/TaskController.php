<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project; 
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:1',
            'status' => 'required|in:Draft,In Progress,Done',
        ]);
        
        $task = Task::create([
            'project_id' => $validated['project_id'],
            'name' => $validated['name'],
            'weight' => $validated['weight'],
            'status' => $validated['status'],
        ]);
        
        // Load Project dengan relasi Tasks
        $project = Project::with('tasks')->find($validated['project_id']);
        
        if ($project) {
            $project->calculateProgress(); 
        }

        $task->project = $project; 

        return response()->json([
            'message' => 'Task berhasil ditambahkan!',
            'task' => $task,
            'project' => $project,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:1',
            'status' => 'required|in:Draft,In Progress,Done',
        ]);

        $task->update($validated);
        
        $project = Project::with('tasks')->find($task->project_id);
        
        if ($project) {
            $project->calculateProgress();
        }

        $task->project = $project;
        
        return response()->json([
            'message' => 'Task berhasil diupdate!',
            'task' => $task,
            'project' => $project,
        ]);
    }

    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();
        
        $project = Project::with('tasks')->find($projectId);
        
        if ($project) {
            $project->calculateProgress();
        }

        return response()->json([
            'message' => 'Task berhasil dihapus!',
            'task_id' => $task->id,
            'project' => $project,
        ]);
    }
}