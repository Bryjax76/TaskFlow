<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Projects') }}
            </h2>

            <a href="{{ route('projects.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                + New Project
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">ID</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Name</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Description</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Tasks</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Created</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse($projects as $project)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-2 text-sm">{{ $project->id }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $project->name }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            {{ Str::limit($project->description, 60) }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span
                                                class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-full font-medium">
                                                {{ $project->tasks_count }} {{ Str::plural('task', $project->tasks_count) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                            {{ optional($project->created_at)->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('projects.show', $project->id) }}"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200 text-xs">
                                                    Show
                                                </a>
                                                <a href="{{ route('projects.edit', $project->id) }}"
                                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-200 text-xs">
                                                    Edit
                                                </a>

                                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this project?')">
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
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            No projects yet. Create your first project! 🚀
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($projects->hasPages())
                        <div class="mt-6">
                            {{ $projects->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>