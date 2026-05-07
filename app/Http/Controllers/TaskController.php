<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = "tasks.index";
        $search = $request->search;

        $tasks = Task::with(['project', 'tags'])
            ->search($search)
            ->when($request->project_id, fn($q, $v) => $q->where('project_id', $v))
            ->when($request->priority, fn($q, $v) => $q->where('priority', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(10);

        $projects = Project::orderBy('name')->get();

        return view($view, compact('tasks', 'search', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $view = "tasks.create";
        $projects = Project::orderBy('name')->get();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $tags = \App\Models\Tag::orderBy('name')->get();

        return view($view, compact('projects', 'employees', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'nullable|string',
            'priority' => 'nullable|integer',
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'color' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $projectId = $validated['project_id'];

        if (!$projectId) {
            $unassignedProject = Project::firstOrCreate(
                ['name' => 'Unassigned'],
                ['description' => 'Default project for unassigned tasks']
            );
            $projectId = $unassignedProject->id;
        }

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'project_id' => $projectId,
            'employee_id' => $validated['employee_id'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'color' => $validated['color'] ?? '#indigo-600',
        ]);

        if (!empty($validated['tags'])) {
            $task->tags()->sync($validated['tags']);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task added 🚀');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $view = 'tasks.show';
        return view($view, compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $projects = Project::orderBy('name')->get();
        $employees = \App\Models\Employee::orderBy('name')->get();
        $tags = \App\Models\Tag::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'projects', 'employees', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'priority' => 'nullable|integer',
            'project_id' => 'nullable|exists:projects,id',
            'employee_id' => 'nullable|exists:employees,id',
            'due_date' => 'nullable|date',
            'color' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $projectId = $validated['project_id'];

        if (!$projectId) {
            $unassignedProject = Project::firstOrCreate(
                ['name' => 'Unassigned'],
                ['description' => 'Default project for unassigned tasks']
            );
            $projectId = $unassignedProject->id;
        }

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'project_id' => $projectId,
            'employee_id' => $validated['employee_id'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'color' => $validated['color'] ?? $task->color,
        ]);

        $task->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated 🔥');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted 🗑️');
    }

    /**
     * Update task status from a dropdown.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:todo,in_progress,done',
        ]);

        $task->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status updated! 🔥');
    }
}
