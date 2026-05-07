<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::withCount('tasks')->latest()->paginate(10);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created 🚀');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['tasks.employees', 'tasks.tags']);

        // Aggregate employees for this project
        $employees = \App\Models\Employee::whereHas('tasks', function($q) use ($project) {
            $q->where('project_id', $project->id);
        })
        ->withCount(['tasks as project_tasks_count' => function($q) use ($project) {
            $q->where('project_id', $project->id);
        }])
        ->withCount(['tasks as finished_tasks_count' => function($q) use ($project) {
            $q->where('project_id', $project->id)->where('status', 'done');
        }])
        ->get();

        return view('projects.show', compact('project', 'employees'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated 🔥');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted 🗑️');
    }
}
