<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Employee') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8 border border-gray-100 max-w-2xl mx-auto">
                <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" id="name" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200"
                            placeholder="e.g. John Doe">
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-semibold text-gray-700 mb-2">Position / Role</label>
                        <input type="text" name="position" id="position"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200"
                            placeholder="e.g. Backend Developer">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200"
                            placeholder="john@example.com">
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-50">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition font-bold shadow-md shadow-indigo-100">
                            Save Employee 👥
                        </button>
                        <a href="{{ route('employees.index') }}"
                            class="text-gray-500 hover:text-gray-700 font-medium transition px-4 py-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
