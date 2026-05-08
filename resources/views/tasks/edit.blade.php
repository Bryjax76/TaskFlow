<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Task #{{ $task->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- ERROR MESSAGES --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tasks.update', $task->id) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- TITLE --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input
                            name="title"
                            value="{{ old('title', $task->title) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            name="description"
                            rows="4"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('description', $task->description) }}</textarea>
                    </div>

                    {{-- PROJECT + EMPLOYEE --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <select
                                name="project_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">— No project (Will be Unassigned) —</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ (old('project_id', $task->project_id) == $project->id) ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                            <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto p-3 border border-gray-300 rounded-lg bg-gray-50 shadow-sm">
                                @foreach ($employees as $employee)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-100 p-1 rounded transition">
                                        <input type="checkbox" name="employees[]" value="{{ $employee->id }}" 
                                            {{ $task->employees->contains($employee->id) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-gray-700">{{ $employee->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- START DATE + DUE DATE --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input
                                type="date"
                                name="start_date"
                                value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input
                                type="date"
                                name="due_date"
                                value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    {{-- COLOR --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Color</label>
                        <input
                            type="color"
                            name="color"
                            value="{{ old('color', $task->color ?? '#4f46e5') }}"
                            class="w-full h-10 p-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    {{-- STATUS + PRIORITY --}}
                    <div class="grid grid-cols-2 gap-4">

                        {{-- STATUS --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>Todo</option>
                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>

                        {{-- PRIORITY --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <input
                                type="number"
                                name="priority"
                                value="{{ old('priority', $task->priority) }}"
                                min="1"
                                max="5"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                    </div>

                    {{-- TAGS --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <div class="flex flex-wrap gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            @forelse($tags as $tag)
                                <label class="inline-flex items-center group cursor-pointer">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                        {{ $task->tags->contains($tag->id) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold text-white transition group-hover:opacity-80 shadow-sm" style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @empty
                                <p class="text-xs text-gray-500 italic">No tags available. <a href="{{ route('tags.index') }}" class="text-indigo-600 hover:underline">Manage tags</a></p>
                            @endforelse
                        </div>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex justify-between mt-6">

                        <a href="{{ route('tasks.index') }}"
                           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Back
                        </a>

                        <button
                            type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 transition">
                            Update Task 💾
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>