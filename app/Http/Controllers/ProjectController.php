<?php

namespace App\Http\Controllers;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        // berdasarkan created_at descending (terbaru)
        $projects = Project::with('tasks')->latest()->get(); 
        return view('project_tracker', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::create(['name' => $validated['name']]);
        
        // Inisialisasi status dan progress
        $project->calculateProgress();
        
        return response()->json([
            'message' => 'Project berhasil ditambahkan!',
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update(['name' => $validated['name']]);

        return response()->json([
            'message' => 'Project berhasil diupdate!',
            'project' => $project,
        ]);
    }
    
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus!',
            'project_id' => $project->id,
        ]);
    }
   
   
}
