<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit project') }}: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.update', $project->id) }}" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project name</label>
                        <input name="name" value="{{ old('name', $project->name) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Project name" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Project description (optional)">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Submit -->
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('projects.index') }}"
                            class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Update project 🔥
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>