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
                    {{ $tasks->total() }} {{ Str::plural('task', $tasks->total()) }} found
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        @php
                            $sortBy = request('sort_by', 'created_at');
                            $sortOrder = request('sort_order', 'desc');

                            function getSortUrl($column, $sortBy, $sortOrder) {
                                $newOrder = ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc';
                                return request()->fullUrlWithQuery(['sort_by' => $column, 'sort_order' => $newOrder]);
                            }

                            function getSortIcon($column, $sortBy, $sortOrder) {
                                if ($sortBy !== $column) return '↕️';
                                return $sortOrder === 'asc' ? '↑' : '↓';
                            }
                        @endphp
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
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
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                        <a href="{{ getSortUrl('status', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                            Status {!! getSortIcon('status', $sortBy, $sortOrder) !!}
                                        </a>
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">
                                        <a href="{{ getSortUrl('project_id', $sortBy, $sortOrder) }}" class="flex items-center gap-1 hover:text-indigo-600">
                                            Project {!! getSortIcon('project_id', $sortBy, $sortOrder) !!}
                                        </a>
                                    </th>
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
                                @forelse($tasks as $task)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-2 text-sm">{{ $task->id }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            {{ $task->title }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ Str::limit($task->description, 50) }}
                                        </td>
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
                                            <form method="POST" action="{{ route('tasks.updateStatus', $task->id) }}"
                                                id="status-form-{{ $task->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()"
                                                    class="text-xs font-medium rounded-full px-2 py-1 border-0 cursor-pointer focus:ring-2 focus:ring-indigo-500
                                                            {{ $task->status === 'done' ? 'bg-green-100 text-green-700' : ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                                    <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>○
                                                        Todo</option>
                                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>⟳ In progress</option>
                                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>✓
                                                        Done</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($task->project)
                                                <a href="{{ route('projects.show', $task->project->id) }}"
                                                    class="text-indigo-600 hover:underline">
                                                    {{ $task->project->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <div class="flex flex-wrap gap-1 mb-2">
                                                @foreach($task->tags as $tag)
                                                    <form action="{{ route('tasks.removeTag', [$task->id, $tag->id]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="group relative px-1.5 py-0.5 rounded text-[10px] font-bold text-white shadow-sm flex items-center gap-1 hover:opacity-80 transition"
                                                            style="background-color: {{ $tag->color }}" title="Unpin Tag">
                                                            {{ $tag->name }}
                                                            <span
                                                                class="text-[8px] opacity-0 group-hover:opacity-100 transition-opacity">✕</span>
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>
                                            <form action="{{ route('tasks.quickAddTag', $task->id) }}" method="POST"
                                                class="flex gap-1">
                                                @csrf
                                                <input type="text" name="tag_name" list="all-tags-list"
                                                    class="text-[10px] py-1 px-2 border-gray-200 rounded w-20 focus:ring-1 focus:ring-indigo-500 focus:w-32 transition-all duration-300"
                                                    placeholder="+ tag" required>
                                                <button type="submit"
                                                    class="bg-gray-100 hover:bg-gray-200 p-1 rounded text-xs"
                                                    title="Add Tag">
                                                    ➕
                                                </button>
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
                                                @if(request('trashed'))
                                                    <form action="{{ route('tasks.restore', $task->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition duration-200 text-xs">
                                                            Restore
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('tasks.forceDelete', $task->id) }}" method="POST" onsubmit="return confirm('PERMANENTLY delete this task?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-red-700 text-white rounded hover:bg-red-800 transition duration-200 text-xs">
                                                            Delete Forever
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('tasks.show', $task->id) }}"
                                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200 text-xs">
                                                        Show
                                                    </a>

                                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                                        class="px-3 py-1 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition duration-200 text-xs">
                                                        Edit
                                                    </a>

                                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200 text-xs">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            @if(request('search'))
                                                No tasks found for "{{ request('search') }}" 😢
                                            @else
                                                No tasks yet. Create your first task! 🚀
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($tasks->hasPages())
                        <div class="mt-6">
                            {{ $tasks->appends(request()->query())->links() }}
                        </div>
                    @endif

                </div>
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