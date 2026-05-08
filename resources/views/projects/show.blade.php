<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $project->name }}
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('projects.edit', $project->id) }}"
                    class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-200">
                    Edit
                </a>
                <a href="{{ route('projects.index') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition duration-200">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Project details --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                <p class="text-gray-600">{{ $project->description ?: 'No description provided.' }}</p>

                <div class="mt-4 text-sm text-gray-500">
                    Created: {{ optional($project->created_at)->format('d.m.Y H:i') }}
                </div>
            </div>

            {{-- Employees Participation --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4 px-1">Team Members ({{ $employees->count() }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($employees as $employee)
                        @php
                            $empProgress = $employee->project_tasks_count > 0
                                ? round(($employee->finished_tasks_count / $employee->project_tasks_count) * 100)
                                : 0;
                        @endphp
                        <div class="bg-white shadow-sm sm:rounded-lg p-5 border border-gray-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                    {{ substr($employee->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 leading-tight">{{ $employee->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $employee->position ?: 'Member' }}</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>Tasks: {{ $employee->project_tasks_count }}</span>
                                    <span>Done: {{ $employee->finished_tasks_count }}</span>
                                </div>
                                <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-green-500 h-2 transition-all duration-500"
                                        style="width: {{ $empProgress }}%"></div>
                                </div>
                                <p class="text-right text-[10px] text-gray-400 font-medium">{{ $empProgress }}%</p>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500 italic border border-dashed border-gray-200">
                            No employees assigned to tasks in this project.
                        </div>
                    @endforelse
                </div>
            </div> {{-- Tab Switcher --}}
            <div class="flex border-b border-gray-200 mb-6">
                <button id="tab-board"
                    class="px-6 py-3 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 transition"
                    onclick="switchTab('board')">
                    Kanban Board
                </button>
                <button id="tab-calendar"
                    class="px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="switchTab('calendar')">
                    Calendar View
                </button>
                <button id="tab-gantt"
                    class="px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition"
                    onclick="switchTab('gantt')">
                    Gantt Chart
                </button>
            </div>

            {{-- Kanban Board Section --}}
            <div id="view-board" class="view-section">
                <div class="flex items-center justify-between mb-6 px-1">
                    <h3 class="text-xl font-bold text-gray-800">Project Board</h3>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <span class="w-3 h-3 bg-gray-300 rounded-full"></span> Todo
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="w-3 h-3 bg-yellow-400 rounded-full"></span> In Progress
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span> Done
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    {{-- Todo Column --}}
                    <div class="kanban-column bg-gray-100/50 rounded-xl p-4 border border-gray-200" data-status="todo">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h4 class="font-bold text-gray-700 uppercase text-xs tracking-wider">To Do</h4>
                            <span
                                class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold task-count">
                                {{ $project->tasks->where('status', 'todo')->count() + $project->tasks->whereNull('status')->count() }}
                            </span>
                        </div>
                        <div class="kanban-tasks space-y-3 min-h-[150px]" id="tasks-todo">
                            @foreach($project->tasks->filter(fn($t) => $t->status === 'todo' || is_null($t->status)) as $task)
                                <x-task-card :task="$task" />
                            @endforeach
                        </div>
                    </div>

                    {{-- In Progress Column --}}
                    <div class="kanban-column bg-yellow-50/50 rounded-xl p-4 border border-yellow-100"
                        data-status="in_progress">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h4 class="font-bold text-yellow-700 uppercase text-xs tracking-wider">In Progress</h4>
                            <span
                                class="bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded text-[10px] font-bold task-count">
                                {{ $project->tasks->where('status', 'in_progress')->count() }}
                            </span>
                        </div>
                        <div class="kanban-tasks space-y-3 min-h-[150px]" id="tasks-in_progress">
                            @foreach($project->tasks->where('status', 'in_progress') as $task)
                                <x-task-card :task="$task" />
                            @endforeach
                        </div>
                    </div>

                    {{-- Done Column --}}
                    <div class="kanban-column bg-green-50/50 rounded-xl p-4 border border-green-100" data-status="done">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h4 class="font-bold text-green-700 uppercase text-xs tracking-wider">Done</h4>
                            <span
                                class="bg-green-200 text-green-800 px-2 py-0.5 rounded text-[10px] font-bold task-count">
                                {{ $project->tasks->where('status', 'done')->count() }}
                            </span>
                        </div>
                        <div class="kanban-tasks space-y-3 min-h-[150px]" id="tasks-done">
                            @foreach($project->tasks->where('status', 'done') as $task)
                                <x-task-card :task="$task" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Calendar View Section --}}
            <div id="view-calendar" class="view-section hidden">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-100">
                    <div id="calendar"></div>
                </div>
            </div>

            {{-- Gantt View Section --}}
            <div id="view-gantt" class="view-section hidden">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-100 overflow-x-auto">
                    <div class="flex justify-end mb-4 gap-2">
                        <button onclick="window.gantt.change_view_mode('Day')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition">Day</button>
                        <button onclick="window.gantt.change_view_mode('Week')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition">Week</button>
                        <button onclick="window.gantt.change_view_mode('Month')"
                            class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded transition">Month</button>
                    </div>
                    <div id="gantt-container">
                        <svg id="gantt-chart"></svg>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Scripts & Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>

    <style>
        .view-section {
            transition: all 0.3s ease;
        }

        .kanban-tasks {
            transition: background-color 0.2s;
        }

        .sortable-ghost {
            opacity: 0.2;
            filter: grayscale(1);
        }

        .sortable-drag {
            transform: rotate(2deg);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        }

        /* FullCalendar Customization */
        #calendar {
            --fc-border-color: #f3f4f6;
            --fc-today-bg-color: #f5f3ff;
            --fc-button-bg-color: #6366f1;
            --fc-button-border-color: #6366f1;
            --fc-button-hover-bg-color: #4f46e5;
            --fc-button-hover-border-color: #4f46e5;
            --fc-button-active-bg-color: #4338ca;
            --fc-button-active-border-color: #4338ca;
            font-size: 0.875rem;
        }

        .fc .fc-toolbar-title {
            font-weight: 700;
            color: #111827;
        }

        .fc .fc-col-header-cell {
            background: #f9fafb;
            padding: 10px 0;
        }

        .fc-event {
            border: none !important;
            border-radius: 4px !important;
            padding: 2px 4px !important;
        }

        /* Gantt Customization */
        .gantt .bar-progress {
            fill: #6366f1 !important;
        }

        .gantt .bar {
            fill: #e5e7eb !important;
        }

        .gantt .label-text {
            font-size: 11px !important;
            font-weight: 600 !important;
        }

        /* Priority Colors for Gantt Bars */
        .gantt-task-priority-1 .bar {
            fill: #9ca3af !important;
        }

        .gantt-task-priority-1 .bar-progress {
            fill: #9ca3af !important;
        }

        .gantt-task-priority-2 .bar {
            fill: #3b82f6 !important;
        }

        .gantt-task-priority-2 .bar-progress {
            fill: #3b82f6 !important;
        }

        .gantt-task-priority-3 .bar {
            fill: #eab308 !important;
        }

        .gantt-task-priority-3 .bar-progress {
            fill: #eab308 !important;
        }

        .gantt-task-priority-4 .bar {
            fill: #f97316 !important;
        }

        .gantt-task-priority-4 .bar-progress {
            fill: #f97316 !important;
        }

        .gantt-task-priority-5 .bar {
            fill: #ff5050 !important;
        }

        .gantt-task-priority-5 .bar-progress {
            fill: #ef4444 !important;
        }
    </style>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
            document.getElementById('view-' + tab).classList.remove('hidden');

            // Update tab buttons
            const tabs = ['board', 'calendar', 'gantt'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-' + t);
                if (t === tab) {
                    btn.classList.add('border-indigo-500', 'text-indigo-600');
                    btn.classList.remove('border-transparent', 'text-gray-500');
                } else {
                    btn.classList.remove('border-indigo-500', 'text-indigo-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
            });

            if (tab === 'calendar' && window.calendar) {
                window.calendar.render();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // --- KANBAN LOGIC ---
            const columns = ['tasks-todo', 'tasks-in_progress', 'tasks-done'];
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            columns.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;

                new Sortable(el, {
                    group: 'tasks',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onAdd: function (evt) {
                        updateTaskStatus(evt.item.dataset.id, evt.to.id.replace('tasks-', ''), evt.item);
                        updateCounts();
                    },
                    onRemove: updateCounts
                });
            });

            function updateTaskStatus(taskId, status, itemEl) {
                itemEl.classList.add('opacity-50');
                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ status: status })
                })
                    .then(res => res.json())
                    .then(() => itemEl.classList.remove('opacity-50'))
                    .catch(() => location.reload());
            }

            function updateCounts() {
                document.querySelectorAll('.kanban-column').forEach(col => {
                    col.querySelector('.task-count').textContent = col.querySelectorAll('.task-card').length;
                });
            }

            // --- CALENDAR LOGIC ---
            const calendarEl = document.getElementById('calendar');
            const priorityColors = {
                1: '#9ca3af',
                2: '#3b82f6',
                3: '#eab308',
                4: '#f97316',
                5: '#ef4444'
            };

            const tasksForCalendar = [
                @foreach($project->tasks as $task)
                    @if($task->due_date)
                        {
                            id: '{{ $task->id }}',
                            title: '{{ $task->title }}',
                            start: '{{ $task->due_date->format('Y-m-d') }}',
                            url: '{{ route('tasks.show', $task->id) }}',
                            backgroundColor: priorityColors[{{ $task->priority ?? 1 }}],
                            borderColor: priorityColors[{{ $task->priority ?? 1 }}],
                        },
                    @endif
                @endforeach
            ];

            window.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                events: tasksForCalendar,
                editable: true,
                eventDrop: function (info) {
                    updateTaskDueDate(info.event.id, info.event.startStr, info);
                },
                eventClick: function (info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },
                height: 'auto',
                firstDay: 1,
            });

            function updateTaskDueDate(taskId, newDate, info) {
                fetch(`/tasks/${taskId}/due-date`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ due_date: newDate })
                })
                    .then(res => res.json())
                    .then(data => { if (!data.success) info.revert(); })
                    .catch(() => info.revert());
            }

            // --- GANTT LOGIC ---
            const ganttTasks = [
                @foreach($project->tasks->filter(fn($t) => $t->due_date) as $task)
                    {
                        id: '{{ $task->id }}',
                        name: '{{ $task->title }}',
                        start: '{{ ($task->start_date ?? $task->created_at)->format('Y-m-d') }}',
                        end: '{{ $task->due_date->format('Y-m-d') }}',
                        progress: {{ $task->status === 'done' ? 100 : ($task->status === 'in_progress' ? 50 : 0) }},
                        custom_class: 'gantt-task-priority-{{ $task->priority ?? 1 }}'
                    },
                @endforeach
            ];

            if (ganttTasks.length > 0) {
                window.gantt = new Gantt("#gantt-chart", ganttTasks, {
                    header_height: 50,
                    column_width: 30,
                    step: 24,
                    view_modes: ['Day', 'Week', 'Month'],
                    bar_height: 25,
                    bar_corner_radius: 3,
                    arrow_curve: 5,
                    padding: 18,
                    view_mode: 'Week',
                    date_format: 'YYYY-MM-DD',
                    on_click: function (task) {
                        window.location.href = `/tasks/${task.id}`;
                    },
                });
            } else {
                document.getElementById('gantt-container').innerHTML = '<p class="text-center py-8 text-gray-500 italic">No tasks with due dates to display in Gantt chart.</p>';
            }
        });
    </script>
</x-app-layout>