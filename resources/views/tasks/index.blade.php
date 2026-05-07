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

                <a href="{{ route('tasks.create') }}"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                    + Add
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
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Description</th>
                                    <th
                                        class="px-4 py-2 text-left text-sm font-semibold text-gray-600 whitespace-nowrap">
                                        Priority
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Project</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Quick Tags</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Created</th>
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
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $task->priority)
                                                    <span class="text-indigo-500 text-lg">●</span>
                                                @else
                                                    <span class="text-gray-300 text-lg">●</span>
                                                @endif
                                            @endfor
                                            <span class="ml-1 text-xs text-gray-500">({{ $task->priority ?? 0 }}/5)</span>
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