<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>

            <div class="flex items-center gap-2">
                @if(request('trashed'))
                    <a href="{{ route('tasks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                        View Active
                    </a>
                @else
                    <a href="{{ route('tasks.index', ['trashed' => 1]) }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                        View Trash 🗑️
                    </a>
                @endif

                <a href="{{ route('tasks.exportPdf', request()->query()) }}"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF Report
                </a>

                <a href="{{ route('tasks.create') }}"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 shadow-sm flex items-center gap-2">
                    <span>+ Add Task</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('tasks.index') }}" class="space-y-3">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        {{-- Search --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search tasks..."
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Project --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Project</label>
                            <select name="project_id"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All projects</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
                            <select name="priority"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All priorities</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ request('priority') == $i ? 'selected' : '' }}>
                                        {{ $i }} / 5
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Date from --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Date to --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 text-sm">
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'project_id', 'priority', 'date_from', 'date_to']))
                                <a href="{{ route('tasks.index') }}"
                                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-sm">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Results info --}}
            @if(request()->hasAny(['search', 'project_id', 'priority', 'date_from', 'date_to']))
                <div class="text-sm text-gray-600 px-1">
                    {{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }} found
                </div>
            @endif

            @php
                $sortBy = request('sort_by', 'created_at');
                $sortOrder = request('sort_order', 'desc');

                if (!function_exists('getSortUrl')) {
                    function getSortUrl($column, $sortBy, $sortOrder) {
                        $newOrder = ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc';
                        return request()->fullUrlWithQuery(['sort_by' => $column, 'sort_order' => $newOrder]);
                    }
                }

                if (!function_exists('getSortIcon')) {
                    function getSortIcon($column, $sortBy, $sortOrder) {
                        if ($sortBy !== $column) return '↕️';
                        return $sortOrder === 'asc' ? '↑' : '↓';
                    }
                }

                $statusGroups = [
                    'todo' => ['label' => 'To Do', 'color' => 'gray', 'icon' => '○'],
                    'in_progress' => ['label' => 'In Progress', 'color' => 'yellow', 'icon' => '⟳'],
                    'done' => ['label' => 'Finished / Done', 'color' => 'green', 'icon' => '✓'],
                ];
            @endphp

            <div class="space-y-8">

                @foreach($statusGroups as $statusKey => $group)
                    @php
                        $groupTasks = $tasks->where('status', $statusKey);
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="text-{{ $group['color'] }}-500 font-bold">{{ $group['icon'] }}</span>
                                    <h3 class="text-lg font-bold text-gray-800 uppercase tracking-wider">{{ $group['label'] }}</h3>
                                    <span class="bg-{{ $group['color'] }}-100 text-{{ $group['color'] }}-700 px-2 py-0.5 rounded-full text-xs font-bold">
                                        {{ $groupTasks->count() }}
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                                <a href="{{ getSortUrl('id', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                                    ID {!! getSortIcon('id', $sortBy, $sortOrder) !!}
                                                </a>
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                                <a href="{{ getSortUrl('title', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                                    Title {!! getSortIcon('title', $sortBy, $sortOrder) !!}
                                                </a>
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Description</th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 whitespace-nowrap">
                                                <a href="{{ getSortUrl('priority', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                                    Priority {!! getSortIcon('priority', $sortBy, $sortOrder) !!}
                                                </a>
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Project</th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Quick Tags</th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                                <a href="{{ getSortUrl('start_date', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                                    Start Date {!! getSortIcon('start_date', $sortBy, $sortOrder) !!}
                                                </a>
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                                <a href="{{ getSortUrl('due_date', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                                    Due Date {!! getSortIcon('due_date', $sortBy, $sortOrder) !!}
                                                </a>
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                                <a href="{{ getSortUrl('created_at', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                                    Created {!! getSortIcon('created_at', $sortBy, $sortOrder) !!}
                                                </a>
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($groupTasks as $task)
                                            <tr class="hover:bg-gray-50 transition duration-150">
                                                <td class="px-4 py-2 text-sm">{{ $task->id }}</td>
                                                <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                                    {{ $task->title }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600">{{ Str::limit($task->description, 50) }}</td>
                                                <td class="px-4 py-2 text-sm whitespace-nowrap">
                                                    @php
                                                        $priorityClasses = [
                                                            1 => 'bg-gray-100 text-gray-600 border-gray-200',
                                                            2 => 'bg-blue-100 text-blue-700 border-blue-200',
                                                            3 => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                                            4 => 'bg-orange-100 text-orange-700 border-orange-200',
                                                            5 => 'bg-red-100 text-red-700 border-red-200',
                                                        ];
                                                        $pClass = $priorityClasses[$task->priority] ?? $priorityClasses[1];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide border {{ $pClass }}">
                                                        {{ $task->priority ?? 0 }} / 5
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($task->project)
                                                        <a href="{{ route('projects.show', $task->project->id) }}" class="text-indigo-600 hover:underline">
                                                            {{ $task->project->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    <div class="flex flex-wrap gap-1 mb-2">
                                                        @foreach($task->tags as $tag)
                                                            <form action="{{ route('tasks.removeTag', [$task->id, $tag->id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="group relative px-1.5 py-0.5 rounded text-[10px] font-bold text-white shadow-sm flex items-center gap-1 hover:opacity-80 transition" style="background-color: {{ $tag->color }}" title="Unpin Tag">
                                                                    {{ $tag->name }}
                                                                    <span class="text-[8px] opacity-0 group-hover:opacity-100 transition-opacity">✕</span>
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                    </div>
                                                    <form action="{{ route('tasks.quickAddTag', $task->id) }}" method="POST" class="flex gap-1">
                                                        @csrf
                                                        <input type="text" name="tag_name" list="all-tags-list"
                                                            class="text-[10px] py-1 px-2 border-gray-200 rounded w-20 focus:ring-1 focus:ring-indigo-500 focus:w-32 transition-all duration-300"
                                                            placeholder="+ tag" required>
                                                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 p-1 rounded text-xs" title="Add Tag">➕</button>
                                                    </form>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                                    {{ $task->start_date ? $task->start_date->format('d.m.Y') : '—' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm whitespace-nowrap">
                                                    @if($task->due_date)
                                                        <span class="{{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                            {{ $task->due_date->format('d.m.Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                                    {{ optional($task->created_at)->format('d.m.Y H:i') }}
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    <div class="flex gap-2">
                                                        <form method="POST" action="{{ route('tasks.updateStatus', $task->id) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="status" onchange="this.form.submit()"
                                                                class="text-[10px] font-bold rounded px-2 py-1 border-gray-200 cursor-pointer focus:ring-1 focus:ring-indigo-500">
                                                                @foreach($statusGroups as $sKey => $sVal)
                                                                    <option value="{{ $sKey }}" {{ $task->status === $sKey ? 'selected' : '' }}>
                                                                        {{ $sVal['label'] }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </form>
                                                        
                                                        <a href="{{ route('tasks.edit', $task->id) }}" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded transition" title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </a>

                                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="px-4 py-6 text-center text-gray-400 italic text-sm">
                                                    No tasks in this category.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- DATALIST FOR TAGS HINTS --}}
    <datalist id="all-tags-list">
        @foreach($allTags as $tag)
            <option value="{{ $tag->name }}">
        @endforeach
    </datalist>
</x-app-layout>