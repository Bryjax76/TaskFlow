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
                        <div class="bg-indigo-500 h-4" style="width: {{ $stats['progress'] }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>