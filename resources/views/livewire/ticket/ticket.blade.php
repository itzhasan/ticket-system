<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tickets</h1>
        <button wire:click="openCreateModal"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create New Ticket
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input wire:model.live="search" type="text" placeholder="Search tickets..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select wire:model.live="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $ticket->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $ticket->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ticket->category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($ticket->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($ticket->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($ticket->status === 'resolved') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ticket->createdBy->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @foreach($ticket->departments as $dept)
                                <span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full mr-1">
                                    {{ $dept->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ticket->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button wire:click="viewTicket({{ $ticket->id }})"
                                    class="text-blue-600 hover:text-blue-900">View</button>

                                <!-- Status Update Dropdown -->
                                <div class="relative inline-block text-left">
                                    <select wire:change="updateTicketStatus({{ $ticket->id }}, $event.target.value)"
                                        class="text-xs border border-gray-300 rounded px-2 py-1">
                                        <option value="">Update Status</option>
                                        <option value="pending" @if($ticket->status === 'pending') selected @endif>Pending
                                        </option>
                                        <option value="in_progress" @if($ticket->status === 'in_progress') selected @endif>In
                                            Progress</option>
                                        <option value="resolved" @if($ticket->status === 'resolved') selected @endif>Resolved
                                        </option>
                                        <option value="closed" @if($ticket->status === 'closed') selected @endif>Closed
                                        </option>
                                    </select>
                                </div>

                                <button wire:click="deleteTicket({{ $ticket->id }})"
                                    onclick="return confirm('Are you sure you want to delete this ticket?')"
                                    class="text-red-600 hover:text-red-900">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No tickets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>

    <!-- Create Ticket Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-transparent bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Ticket</h3>

                    <form wire:submit.prevent="createTicket">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input wire:model="title" type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-transparent mb-2">Category</label>
                            <select wire:model.live="categoryId" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        @if($categoryId)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                                <select wire:model.live="templateId" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="">Select Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                                @error('templateId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Departments</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select wire:model.live="departmentIds"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($errors->has('departmentIds'))
                                <span class="text-red-500 text-sm">{{ $errors->first('departmentIds') }}</span>
                            @endif
                        </div>

                        <!-- Template Fields -->
                        @if(!empty($templateFields))
                            <div class="mb-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Template Fields</h4>
                                @foreach($templateFields as $field)
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $field['name'] }}
                                            @if($field['required']) <span class="text-red-500">*</span> @endif
                                        </label>

                                        @if($field['type'] === 'text')
                                            <input wire:model="fieldValues.{{ $field['id'] }}" type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        @elseif($field['type'] === 'textarea')
                                            <textarea wire:model="fieldValues.{{ $field['id'] }}" rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                                        @elseif($field['type'] === 'select')
                                            <select wire:model="fieldValues.{{ $field['id'] }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="">Select Option</option>
                                                @if($field['options'])
                                                    @foreach(json_decode($field['options']) ?? [] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field['type'] === 'date')
                                            <input wire:model="fieldValues.{{ $field['id'] }}" type="date"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        @endif

                                        @error('fieldValues.' . $field['id'])
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-end space-x-4 mt-6">
                            <button type="button" wire:click="closeCreateModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Create Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
