<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add new task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5">
                    @csrf
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input name="title"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Task title" required>
                    </div>
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Task description"></textarea>
                    </div>
                    <!-- Project + Employee -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <select name="project_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— No project (Will be Unassigned) —</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                            <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto p-3 border border-gray-300 rounded-lg bg-gray-50 shadow-sm">
                                @foreach ($employees as $employee)
                                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-100 p-1 rounded transition">
                                        <input type="checkbox" name="employees[]" value="{{ $employee->id }}" 
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-gray-700">{{ $employee->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Start Date + Due Date -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" name="due_date"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Task Color</label>
                        <input type="color" name="color" value="#4f46e5"
                            class="w-full h-10 p-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <!-- Status + Priority grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="todo">Todo</option>
                                <option value="in_progress">In progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <input type="number" name="priority" value="1" min="1" max="5"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <div class="flex flex-wrap gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            @forelse($tags as $tag)
                                <label class="inline-flex items-center group cursor-pointer">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
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

                    <!-- Action Buttons -->
                    <div class="flex justify-between mt-8 pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Add task 🚀
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>