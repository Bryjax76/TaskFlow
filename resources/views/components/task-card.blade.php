@props(['task'])

<div class="task-card bg-white p-4 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition duration-200 cursor-grab active:cursor-grabbing select-none" 
     data-id="{{ $task->id }}">
    
    <div class="flex justify-between items-start mb-2">
        <h5 class="text-sm font-bold text-gray-900 leading-tight">
            <a href="{{ route('tasks.show', $task->id) }}" class="hover:text-indigo-600 transition" draggable="false">
                {{ $task->title }}
            </a>
        </h5>
        <div class="flex flex-shrink-0 ml-2">
            @php
                $priorityClasses = [
                    1 => 'bg-gray-50 text-gray-500',
                    2 => 'bg-blue-50 text-blue-600',
                    3 => 'bg-yellow-50 text-yellow-600',
                    4 => 'bg-orange-50 text-orange-600',
                    5 => 'bg-red-50 text-red-600',
                ];
                $pClass = $priorityClasses[$task->priority] ?? $priorityClasses[1];
            @endphp
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold tracking-tight {{ $pClass }}">
                P{{ $task->priority ?? 0 }}
            </span>
        </div>
    </div>

    @if($task->description)
        <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">
            {{ $task->description }}
        </p>
    @endif

    <div class="flex flex-wrap gap-1 mb-3">
        @foreach($task->tags as $tag)
            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium" 
                  style="background-color: {{ $tag->color }}15; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}30">
                {{ $tag->name }}
            </span>
        @endforeach
    </div>

    <div class="flex justify-between items-center">
        <div class="flex -space-x-1.5 overflow-hidden">
            @foreach($task->employees as $employee)
                <div class="inline-block h-5 w-5 rounded-full ring-2 ring-white bg-indigo-100 text-indigo-700 flex items-center justify-center text-[8px] font-bold" 
                     title="{{ $employee->name }}">
                    {{ substr($employee->name, 0, 1) }}
                </div>
            @endforeach
            @if($task->employees->isEmpty())
                <span class="text-[10px] text-gray-400">Unassigned</span>
            @endif
        </div>
        
        <span class="text-[10px] text-gray-400 font-medium">
            #{{ $task->id }}
        </span>
    </div>
</div>
