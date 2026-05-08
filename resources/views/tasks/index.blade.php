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

                        {{-- Status --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select name="status"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All statuses</option>
                                <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>○ To Do</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>⟳ In Progress</option>
                                <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>✓ Finished</option>
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
                        // If a status filter is applied, skip other groups
                        if (request('status') && request('status') !== $statusKey) {
                            continue;
                        }
                        
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
                                                        
                                                        <button type="button" 
                                                            onclick="openPreviewModal({{ json_encode($task->load(['project', 'tags', 'employees'])) }})"
                                                            class="p-1 text-blue-600 hover:bg-blue-50 rounded transition" title="Quick Preview">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>

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

    {{-- PREVIEW MODAL --}}
    <div id="preview-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="closePreviewModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white" id="modal-task-title">Task Details</h3>
                    <button onclick="closePreviewModal()" class="text-indigo-100 hover:text-white transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Project</h4>
                            <div id="modal-task-project" class="text-gray-900 font-medium bg-gray-50 p-2 rounded border border-gray-100"></div>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Priority & Status</h4>
                            <div class="flex gap-2">
                                <span id="modal-task-priority" class="px-3 py-1 rounded-full text-xs font-bold border"></span>
                                <span id="modal-task-status" class="px-3 py-1 rounded-full text-xs font-bold border capitalize"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Description</h4>
                        <div id="modal-task-description" class="text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100 whitespace-pre-wrap leading-relaxed"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Timeline</h4>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-600">Start: <span id="modal-task-start" class="font-bold text-gray-900"></span></p>
                                <p class="text-gray-600">Due: <span id="modal-task-due" class="font-bold text-gray-900"></span></p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Assigned Team</h4>
                            <div id="modal-task-employees" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tags</h4>
                        <div id="modal-task-tags" class="flex flex-wrap gap-2"></div>
                    </div>

                    {{-- Comments in Modal --}}
                    <div class="border-t pt-6">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            Discussion <span id="modal-comments-count" class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[10px]">0</span>
                        </h4>
                        
                        {{-- Comment Form --}}
                        <form action="{{ route('comments.store') }}" method="POST" class="mb-6">
                            @csrf
                            <input type="hidden" name="task_id" id="modal-task-id-input">
                            <div class="flex gap-2">
                                <textarea name="content" rows="1" 
                                    class="flex-1 border-gray-200 rounded-lg text-sm p-2 bg-gray-50 focus:bg-white transition focus:ring-indigo-500 focus:border-indigo-500" 
                                    placeholder="Add a comment..."></textarea>
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </div>
                        </form>

                        <div id="modal-task-comments" class="space-y-4 max-h-60 overflow-y-auto pr-2 custom-scrollbar"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                    <a id="modal-edit-link" href="#" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm">
                        Edit Full Task
                    </a>
                    <button onclick="closePreviewModal()" class="bg-white text-gray-700 border border-gray-300 px-6 py-2 rounded-lg font-bold hover:bg-gray-50 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPreviewModal(task) {
            console.log('Previewing task:', task);
            document.getElementById('modal-task-title').innerText = `#${task.id} - ${task.title}`;
            document.getElementById('modal-task-id-input').value = task.id;
            document.getElementById('modal-task-project').innerText = task.project ? task.project.name : 'No Project';
            document.getElementById('modal-task-description').innerText = task.description || 'No description provided.';
            
            // Priority
            const priorityEl = document.getElementById('modal-task-priority');
            const priorityClasses = {
                1: 'bg-gray-100 text-gray-600 border-gray-200',
                2: 'bg-blue-100 text-blue-700 border-blue-200',
                3: 'bg-yellow-100 text-yellow-700 border-yellow-200',
                4: 'bg-orange-100 text-orange-700 border-orange-200',
                5: 'bg-red-100 text-red-700 border-red-200',
            };
            priorityEl.innerText = `Priority ${task.priority}/5`;
            priorityEl.className = `px-3 py-1 rounded-full text-xs font-bold border ${priorityClasses[task.priority] || priorityClasses[1]}`;

            // Status
            const statusEl = document.getElementById('modal-task-status');
            const statusClasses = {
                'todo': 'bg-gray-100 text-gray-700 border-gray-200',
                'in_progress': 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'done': 'bg-green-100 text-green-700 border-green-200'
            };
            statusEl.innerText = task.status.replace('_', ' ');
            statusEl.className = `px-3 py-1 rounded-full text-xs font-bold border ${statusClasses[task.status] || statusClasses.todo}`;

            // Dates
            document.getElementById('modal-task-start').innerText = task.start_date ? formatDate(task.start_date) : '—';
            document.getElementById('modal-task-due').innerText = task.due_date ? formatDate(task.due_date) : '—';

            // Employees
            const empContainer = document.getElementById('modal-task-employees');
            empContainer.innerHTML = '';
            if (task.employees && task.employees.length > 0) {
                task.employees.forEach(emp => {
                    const span = document.createElement('span');
                    span.className = 'px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-medium border border-indigo-100';
                    span.innerText = emp.name;
                    empContainer.appendChild(span);
                });
            } else {
                empContainer.innerHTML = '<span class="text-gray-400 text-xs italic">Unassigned</span>';
            }

            // Tags
            const tagContainer = document.getElementById('modal-task-tags');
            tagContainer.innerHTML = '';
            if (task.tags && task.tags.length > 0) {
                task.tags.forEach(tag => {
                    const span = document.createElement('span');
                    span.className = 'px-2 py-0.5 rounded text-[10px] font-bold text-white shadow-sm';
                    span.style.backgroundColor = tag.color;
                    span.innerText = tag.name;
                    tagContainer.appendChild(span);
                });
            } else {
                tagContainer.innerHTML = '<span class="text-gray-400 text-xs italic">No tags</span>';
            }

            // Comments
            const commentsContainer = document.getElementById('modal-task-comments');
            const commentsCount = document.getElementById('modal-comments-count');
            commentsContainer.innerHTML = '';
            const comments = task.comments || [];
            commentsCount.innerText = comments.length;

            if (comments.length > 0) {
                comments.forEach(comment => {
                    const div = document.createElement('div');
                    div.className = 'flex gap-3';
                    div.innerHTML = `
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 text-xs font-bold border border-gray-200">
                            ${comment.user ? comment.user.name.substring(0,1) : '?'}
                        </div>
                        <div class="flex-1 bg-white border border-gray-100 p-3 rounded-xl shadow-sm">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-gray-900">${comment.user ? comment.user.name : 'Unknown User'}</span>
                                <span class="text-[9px] text-gray-400">${new Date(comment.created_at).toLocaleString()}</span>
                            </div>
                            <p class="text-xs text-gray-700 leading-relaxed">${comment.content}</p>
                        </div>
                    `;
                    commentsContainer.appendChild(div);
                });
            } else {
                commentsContainer.innerHTML = '<p class="text-center text-gray-400 text-xs py-4 italic">No comments yet. Start the conversation!</p>';
            }

            // Edit link
            document.getElementById('modal-edit-link').href = `/tasks/${task.id}/edit`;

            // Show modal
            document.getElementById('preview-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function closePreviewModal() {
            document.getElementById('preview-modal').classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scroll
        }

        function formatDate(dateStr) {
            if (!dateStr) return '—';
            const date = new Date(dateStr);
            return date.toLocaleDateString('pl-PL'); // Or your preferred locale
        }

        // Close on ESC
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePreviewModal();
        });
    </script>

    {{-- DATALIST FOR TAGS HINTS --}}
    <datalist id="all-tags-list">
        @foreach($allTags as $tag)
            <option value="{{ $tag->name }}">
        @endforeach
    </datalist>
</x-app-layout>