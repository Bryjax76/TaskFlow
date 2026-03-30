<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('tasks.create') }}"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    + Add
                </a>

                <input type="text" placeholder="Search..." class="border-gray-300 rounded-lg px-3 py-2">
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="p-6 text-gray-900">

                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Description</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 whitespace-nowrap">Priority</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Created</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @forelse($tasks as $task)
                                    <tr class="hover:bg-gray-50">

                                        <td class="px-4 py-2 text-sm">{{ $task->id }}</td>
                                        <td class="px-4 py-2 text-sm font-medium">{{ $task->title }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $task->description }}</td>
                                        <td class="px-4 py-2 text-sm whitespace-nowrap">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $task->priority)
                                                    <span class="text-indigo-500">●</span>
                                                @else
                                                    <span class="text-gray-300">●</span>
                                                @endif
                                            @endfor
                                        </td>
                                        {{-- STATUS --}}
                                        <td class="px-4 py-2 text-sm">
                                            @if($task->status === 'done')
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
                                                    Done
                                                </span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full">
                                                    In progress
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                                    Todo
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-2 text-sm">
                                            {{ $task->created_at }}
                                        </td>

                                        {{-- ACTIONS --}}
                                        <td class="px-4 py-2 text-sm flex gap-2">

                                            {{-- EDIT --}}
                                            <a href="{{ route('tasks.edit', $task->id) }}"
                                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Edit
                                            </a>

                                            {{-- DELETE --}}
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                onsubmit="return confirm('Na pewno usunąć task?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                                    Delete
                                                </button>
                                            </form>

                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                            No tasks 😢
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>