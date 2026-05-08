<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">Todo</p>
                            <p class="text-2xl font-bold">{{ $stats['todo'] }}</p>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">In progress</p>
                            <p class="text-2xl font-bold">{{ $stats['in_progress'] }}</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">Done</p>
                            <p class="text-2xl font-bold">{{ $stats['done'] }}</p>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">Progress</p>
                            <p class="text-2xl font-bold">{{ $stats['progress'] }}%</p>
                        </div>

                    </div>
                    <div class="mt-6 bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="bg-indigo-500 h-4 transition-all duration-500" style="width: {{ $stats['progress'] }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="text-lg font-semibold text-gray-800">Projects Overview</h3>
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        Drag to reorder
                    </span>
                </div>
                <div id="projects-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        @php
                            $projectProgress = $project->tasks_count > 0 
                                ? round(($project->done_tasks_count / $project->tasks_count) * 100) 
                                : 0;
                        @endphp
                        <div class="project-card bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition duration-200 select-none"
                             data-id="{{ $project->id }}"
                             style="cursor: grab;">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-start gap-2">
                                        <div class="drag-handle mt-1 text-gray-300 hover:text-gray-500 transition flex-shrink-0" title="Drag to reorder">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                                                <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                                                <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900">{{ $project->name }}</h4>
                                            <p class="text-sm text-gray-500 line-clamp-1">{{ $project->description }}</p>
                                        </div>
                                    </div>
                                    <span class="flex-shrink-0 px-2 py-1 text-xs font-semibold rounded-full {{ $projectProgress == 100 ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $projectProgress }}%
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Total Tasks</p>
                                        <p class="text-xl font-bold text-gray-800">{{ $project->tasks_count }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Completed</p>
                                        <p class="text-xl font-bold text-gray-800">{{ $project->done_tasks_count }}</p>
                                    </div>
                                </div>

                                <div class="relative pt-1">
                                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-100">
                                        <div style="width:{{ $projectProgress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $projectProgress == 100 ? 'bg-green-500' : 'bg-indigo-500' }} transition-all duration-500"></div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mt-2">
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition" draggable="false">
                                        View Details →
                                    </a>
                                    <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition" draggable="false">
                                        Tasks List
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($projects->isEmpty())
                        <div class="col-span-full bg-white p-12 text-center rounded-lg shadow-sm border border-dashed border-gray-300">
                            <p class="text-gray-500 mb-4">No projects found. Start by creating one!</p>
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                                + Create Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <style>
        .project-card.sortable-ghost {
            opacity: 0.4;
            border: 2px dashed #6366f1;
            background: #eef2ff !important;
        }
        .project-card.sortable-drag {
            cursor: grabbing !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
            transform: rotate(1.5deg) scale(1.02);
        }
        .project-card:active {
            cursor: grabbing;
        }
    </style>
    <script>
        (function () {
            const STORAGE_KEY = 'dashboard_project_order';
            const grid = document.getElementById('projects-grid');

            if (!grid) return;

            // Restore saved order
            function restoreOrder() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return;
                try {
                    const order = JSON.parse(saved);
                    const cards = Array.from(grid.querySelectorAll('.project-card'));
                    order.forEach(function (id) {
                        const card = cards.find(c => c.dataset.id === String(id));
                        if (card) grid.appendChild(card);
                    });
                } catch (e) { /* ignore */ }
            }

            // Save current order
            function saveOrder() {
                const cards = Array.from(grid.querySelectorAll('.project-card'));
                const order = cards.map(c => c.dataset.id);
                localStorage.setItem(STORAGE_KEY, JSON.stringify(order));
            }

            restoreOrder();

            Sortable.create(grid, {
                animation: 180,
                easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: saveOrder,
            });
        })();
    </script>

</x-app-layout>