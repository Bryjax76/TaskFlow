<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Task #{{ $task->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

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

                    {{-- DESCRIPTION --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea
                            name="description"
                            rows="4"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('description', $task->description) }}</textarea>
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