<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">Todo</p>
                            <p class="text-2xl font-bold">{{ $stats['todo'] }}</p>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">In progress</p>
                            <p class="text-2xl font-bold">{{ $stats['in_progress'] }}</p>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">Done</p>
                            <p class="text-2xl font-bold">{{ $stats['done'] }}</p>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-lg shadow">
                            <p class="text-sm text-gray-500">Progress</p>
                            <p class="text-2xl font-bold">{{ $stats['progress'] }}%</p>
                        </div>

                    </div>
                    <div class="mt-6 bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="bg-indigo-500 h-4 transition-all duration-500" style="width: {{ $stats['progress'] }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 px-1">Projects Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                        @php
                            $projectProgress = $project->tasks_count > 0 
                                ? round(($project->done_tasks_count / $project->tasks_count) * 100) 
                                : 0;
                        @endphp
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition duration-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900">{{ $project->name }}</h4>
                                        <p class="text-sm text-gray-500 line-clamp-1">{{ $project->description }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $projectProgress == 100 ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $projectProgress }}%
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Total Tasks</p>
                                        <p class="text-xl font-bold text-gray-800">{{ $project->tasks_count }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase font-semibold">Completed</p>
                                        <p class="text-xl font-bold text-gray-800">{{ $project->done_tasks_count }}</p>
                                    </div>
                                </div>

                                <div class="relative pt-1">
                                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-100">
                                        <div style="width:{{ $projectProgress }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $projectProgress == 100 ? 'bg-green-500' : 'bg-indigo-500' }} transition-all duration-500"></div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mt-2">
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                                        View Details →
                                    </a>
                                    <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                                        Tasks List
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($projects->isEmpty())
                        <div class="col-span-full bg-white p-12 text-center rounded-lg shadow-sm border border-dashed border-gray-300">
                            <p class="text-gray-500 mb-4">No projects found. Start by creating one!</p>
                            <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                                + Create Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>