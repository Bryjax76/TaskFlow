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
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
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
                                    <div class="bg-green-500 h-2 transition-all duration-500" style="width: {{ $empProgress }}%"></div>
                                </div>
                                <p class="text-right text-[10px] text-gray-400 font-medium">{{ $empProgress }}%</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500 italic border border-dashed border-gray-200">
                            No employees assigned to tasks in this project.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tasks belonging to this project --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Tasks ({{ $project->tasks->count() }})
                </h3>

                @if($project->tasks->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Assigned To</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Priority</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($project->tasks as $task)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-2 text-sm">{{ $task->id }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            <a href="{{ route('tasks.show', $task->id) }}"
                                                class="text-indigo-600 hover:underline">
                                                {{ $task->title }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($task->status === 'done')
                                                <span
                                                    class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">✓
                                                    Done</span>
                                            @elseif($task->status === 'in_progress')
                                                <span
                                                    class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-medium">⟳
                                                    In progress</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full font-medium">○
                                                    Todo</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <div class="flex -space-x-2 overflow-hidden">
                                                @forelse($task->employees as $employee)
                                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-indigo-500 text-white flex items-center justify-center text-[10px] font-bold" title="{{ $employee->name }}">
                                                        {{ substr($employee->name, 0, 1) }}
                                                    </div>
                                                @empty
                                                    <span class="text-gray-400 text-xs">—</span>
                                                @endforelse
                                            </div>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No tasks assigned to this project yet.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>