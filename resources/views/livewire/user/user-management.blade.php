<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <button wire:click="$set('showCreateForm', true)"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add New User
                </button>
            </div>

            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Create User Form -->
            @if($showCreateForm)
                <livewire:auth.register />
            @endif

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categories
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <select
                                        wire:change="updateDepartment($event.target.value,{{$user->id}})"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                @if($user->department_id === $department->id) selected @endif>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                        <option value="Low">Low</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($user->categories as $category)
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                {{ $category->name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400">No categories</span>
                                        @endforelse
                                    </div>
                                    <button wire:click="openCategoryModal({{ $user->id }})"
                                        class="mt-1 text-xs text-blue-600 hover:text-blue-900">
                                        Manage Categories
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($user->id !== auth()->id())
                                        <button wire:click="deleteUser({{ $user->id }})"
                                            class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    @else
                                        <span class="text-gray-400">Current User</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Category Management Modal -->
    @if($showCategoryModal)
        <div class="fixed inset-0 bg-transparent bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Manage User Categories
                        </h3>
                        <button wire:click="closeCategoryModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="updateUserCategories">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Select Categories
                            </label>
                            <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded p-3">
                                @foreach($allCategories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="closeCategoryModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Update Categories
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
