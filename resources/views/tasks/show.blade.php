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

            {{-- Comments Section --}}
            <div class="mt-12 border-t pt-8">
                <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Discussion ({{ $task->comments->count() }})
                </h4>

                {{-- Post a comment --}}
                <div class="mb-8">
                    <form action="{{ route('comments.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $task->id }}">
                        <textarea name="content" rows="3" 
                            class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-4 bg-gray-50 transition duration-200 focus:bg-white"
                            placeholder="Write a message or update..." required></textarea>
                        <div class="flex justify-end">
                            <button type="submit" 
                                class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                                <span>Post Comment</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Comments list --}}
                <div class="space-y-6">
                    @forelse($task->comments as $comment)
                        <div class="flex gap-4 group">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border-2 border-white shadow-sm">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="bg-white border border-gray-100 p-4 rounded-2xl shadow-sm group-hover:shadow-md transition duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold text-gray-900 text-sm">{{ $comment->user->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 text-sm leading-relaxed">
                                        {{ $comment->content }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <div class="text-gray-400 mb-2 italic">No comments yet.</div>
                            <p class="text-xs text-gray-500">Be the first to start the discussion!</p>
                        </div>
                    @endforelse
                </div>
            </div>
</x-app-layout>