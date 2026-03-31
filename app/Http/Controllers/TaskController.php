<?php

namespace App\Http\Controllers;

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

        $tasks = Task::search($search)
            ->latest()
            ->paginate(10);

        return view($view, compact('tasks', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $view = "tasks.create";

        return view($view);
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
        ]);

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
        ]);

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
        return view('tasks.edit', compact('task'));
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
        ]);

        $task->update($validated);

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
            ->with('success', 'Task usunięty 🗑️');
    }
}
