<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        $stats = [
            'todo' => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'done' => $tasks->where('status', 'done')->count(),
            'total' => $tasks->count(),
        ];

        $stats['progress'] = $stats['total']
            ? round(($stats['done'] / $stats['total']) * 100)
            : 0;

        $projects = \App\Models\Project::withCount([
            'tasks',
            'tasks as done_tasks_count' => function ($query) {
                $query->where('status', 'done');
            }
        ])->get();

        return view('dashboard', compact('stats', 'projects'));
    }
}