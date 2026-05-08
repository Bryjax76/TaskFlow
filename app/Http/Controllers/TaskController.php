<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = "tasks.index";
        $search = $request->search;

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Valid sortable columns
        $validColumns = ['id', 'title', 'priority', 'status', 'project_id', 'start_date', 'due_date', 'created_at'];
        if (!in_array($sortBy, $validColumns)) {
            $sortBy = 'created_at';
        }

        $tasks = Task::with(['project', 'tags', 'employees', 'comments.user'])
            ->search($search)
            ->when($request->project_id, fn($q, $v) => $q->where('project_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->priority, fn($q, $v) => $q->where('priority', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->trashed, fn($q) => $q->onlyTrashed())
            ->orderBy($sortBy, $sortOrder)
            ->get();

        $projects = Project::orderBy('name')->get();
        $allTags = \App\Models\Tag::orderBy('name')->get();

        return view($view, compact('tasks', 'search', 'projects', 'allTags'));
    }

    /**
     * Export tasks to PDF.
     */
    public function exportPdf(Request $request)
    {
        $search = $request->search;
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $tasks = Task::with(['project', 'tags', 'employees'])
            ->search($search)
            ->when($request->project_id, fn($q, $v) => $q->where('project_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->priority, fn($q, $v) => $q->where('priority', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->trashed, fn($q) => $q->onlyTrashed())
            ->orderBy($sortBy, $sortOrder)
            ->get();

        // Stats for Chart - Should ignore Status filter but keep Project filter
        $statsTasks = Task::search($search)
            ->when($request->project_id, fn($q, $v) => $q->where('project_id', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->get();

        $total = $statsTasks->count();
        $todo = $statsTasks->where('status', 'todo')->count();
        $inProgress = $statsTasks->where('status', 'in_progress')->count();
        $done = $statsTasks->where('status', 'done')->count();
        $completionRate = $total > 0 ? round(($done / $total) * 100) : 0;

        // Generate QuickChart URL (Pie Chart)
        $chartData = [
            'type' => 'pie',
            'data' => [
                'labels' => ['Todo', 'In Progress', 'Done'],
                'datasets' => [[
                    'data' => [$todo, $inProgress, $done],
                    'backgroundColor' => ['#9ca3af', '#facc15', '#22c55e']
                ]]
            ],
            'options' => [
                'title' => [
                    'display' => true,
                    'text' => 'Task Status Distribution'
                ]
            ]
        ];
        $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartData));

        // Fetch image and convert to base64 for reliable PDF rendering
        try {
            $imageContent = file_get_contents($chartUrl);
            $base64Image = 'data:image/png;base64,' . base64_encode($imageContent);
        } catch (\Exception $e) {
            $base64Image = null; // Fallback
        }

        $pdf = Pdf::loadView('tasks.pdf', compact('tasks', 'total', 'todo', 'inProgress', 'done', 'completionRate', 'base64Image'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('task-report-' . now()->format('Y-m-d') . '.pdf');
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

        // Smart Defaults for Dates
        $now = now();
        
        // Default Start Date: Next working day (Mon-Fri)
        $defaultStartDate = $now->copy()->addDay();
        while ($defaultStartDate->isWeekend()) {
            $defaultStartDate->addDay();
        }
        
        // Default Due Date: Friday of the week the task STARTS
        $defaultDueDate = $defaultStartDate->copy()->endOfWeek()->subDays(2);
        
        // If the task starts on a Friday, set due date to next Friday? 
        // Actually, Friday of the same week is fine if it starts on Mon-Thu.
        // If it starts on Friday, let's make it next Friday.
        if ($defaultStartDate->isFriday()) {
            $defaultDueDate->addWeek();
        }

        return view($view, compact('projects', 'employees', 'tags', 'defaultStartDate', 'defaultDueDate'));
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
            'employees' => 'nullable|array',
            'employees.*' => 'exists:employees,id',
            'start_date' => 'nullable|date',
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

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'project_id' => $projectId,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'color' => $validated['color'] ?? '#indigo-600',
        ]);

        if (!empty($validated['tags'])) {
            $task->tags()->sync($validated['tags']);
        }

        if (!empty($validated['employees'])) {
            $task->employees()->sync($validated['employees']);
        }

        if ($request->input('action') === 'save_and_another') {
            return redirect()->route('tasks.create', ['project_id' => $task->project_id])
                ->with('success', 'Task added! You can now add another one. ➕');
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
            'employees' => 'nullable|array',
            'employees.*' => 'exists:employees,id',
            'start_date' => 'nullable|date',
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
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'color' => $validated['color'] ?? $task->color,
        ]);

        $task->tags()->sync($validated['tags'] ?? []);
        $task->employees()->sync($validated['employees'] ?? []);

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

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated! 🔥',
                'status' => $task->status
            ]);
        }

        return redirect()->back()->with('success', 'Status updated! 🔥');
    }

    /**
     * Update task due date via AJAX.
     */
    public function updateDueDate(Request $request, Task $task)
    {
        $validated = $request->validate([
            'due_date' => 'required|date',
        ]);

        $task->update(['due_date' => $validated['due_date']]);

        return response()->json([
            'success' => true,
            'message' => 'Due date updated! 📅',
            'due_date' => $task->due_date->format('Y-m-d')
        ]);
    }

    /**
     * Quick add a tag to a task.
     */
    public function quickAddTag(Request $request, Task $task)
    {
        $validated = $request->validate([
            'tag_name' => 'required|string|max:255',
        ]);

        $tagName = trim($validated['tag_name']);

        // Find or create the tag
        $tag = \App\Models\Tag::firstOrCreate(
            ['name' => $tagName],
            ['color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT)] // Random color for new tags
        );

        // Attach if not already attached
        if (!$task->tags->contains($tag->id)) {
            $task->tags()->attach($tag->id);
        }

        return redirect()->back()->with('success', "Tag '{$tagName}' added! 🏷️");
    }

    /**
     * Remove (unpin) a tag from a task.
     */
    public function removeTag(Task $task, \App\Models\Tag $tag)
    {
        $task->tags()->detach($tag->id);
        return redirect()->back()->with('success', "Tag '{$tag->name}' removed! 💨");
    }

    /**
     * Restore a soft-deleted task.
     */
    public function restore($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return redirect()->back()->with('success', 'Task restored! 🔄');
    }

    /**
     * Permanently delete a task.
     */
    public function forceDelete($id)
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->forceDelete();

        return redirect()->back()->with('success', 'Task permanently deleted! 🗑️');
    }
}
