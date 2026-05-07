<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tags Management') }}
            </h2>
            <a href="{{ route('tags.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm text-sm font-medium">
                + Create Tag
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Preview</th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($tags as $tag)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                            {{ $tag->name }}
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm"
                                                style="background-color: {{ $tag->color }}">
                                                {{ $tag->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <div class="flex gap-3">
                                                <a href="{{ route('tags.edit', $tag->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium transition">Edit</a>
                                                <form action="{{ route('tags.destroy', $tag->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 font-medium transition">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-12 text-center text-gray-500 italic">
                                            No tags created yet.
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