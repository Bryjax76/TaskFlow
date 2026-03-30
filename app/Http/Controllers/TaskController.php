<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $view = "tasks.index";
        $tasks = Task::all();

        return view($view, compact('tasks'));
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
            'title'=> 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'nullable|string',
            'priority'=> 'nullable|integer',
        ]);

        Task::create([
            'title'=> $validated['title'],
            'description'=> $validated['description'],
            'status'=> $validated['status'],
            'priority'=> $validated['priority'],
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task added 🚀');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
