<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Task #{{ $task->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">

            <h3 class="text-2xl font-bold mb-4">
                {{ $task->title }}
            </h3>

            <p class="text-gray-600 mb-4">
                {{ $task->description }}
            </p>

            <div class="flex gap-4 text-sm mb-4">
                <span><strong>Status:</strong> {{ $task->status }}</span>
                <span><strong>Priority:</strong> {{ $task->priority }}/5</span>
            </div>

            <div class="text-sm text-gray-500">
                Created: {{ $task->created_at?->format('d.m.Y H:i') }}
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('tasks.edit', $task->id) }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded">
                    Edit
                </a>

                <a href="{{ route('tasks.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded">
                    Back
                </a>
            </div>

        </div>
    </div>
</x-app-layout>