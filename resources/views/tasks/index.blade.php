<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks') }}
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('tasks.create') }}"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                    + Add
                </a>

                <form method="GET" action="{{ route('tasks.index') }}" class="flex gap-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search tasks..." 
                           class="border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                    @if(request('search'))
                        <a href="{{ route('tasks.index') }}"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                            Clear
                        </a>
                    @endif

                    <button type="submit" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Results info --}}
                    @if(request('search'))
                        <div class="mb-4 text-sm text-gray-600">
                            Showing results for: <strong>"{{ request('search') }}"</strong>
                            ({{ $tasks->total() }} {{ Str::plural('task', $tasks->total()) }} found)
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Title</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Description</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 whitespace-nowrap">
                                        Priority
                                    </th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Created</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse($tasks as $task)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-2 text-sm">{{ $task->id }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $task->title }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ Str::limit($task->description, 50) }}</td>
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
                                            @if($task->status === 'done')
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">
                                                    ✓ Done
                                                </span>
                                            @elseif($task->status === 'in_progress')
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-medium">
                                                    ⟳ In progress
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full font-medium">
                                                    ○ Todo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                            {{ optional($task->created_at)->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('tasks.show', $task->id) }}"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200 text-xs">
                                                    Show
                                                </a>

                                                <form action="{{ route('tasks.destroy', $task->id) }}" 
                                                      method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200 text-xs">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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
</x-app-layout>