<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tickets</h1>
        <button wire:click="openCreateModal"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create New Ticket
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input wire:model.live.debounce.500ms="search" type="text" placeholder="Search tickets..."
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
            <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Priority {{$priority}}</label>
                <select wire:model.live="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="all">All Properties</option>
                    @foreach($priorityOptions as $priority)
                        <option value="{{ $priority }}">{{ $priority }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
        <div wire:loading.delay wire:target="search,statusFilter,categoryFilter,priority,updateTicketStatus,viewTicket"
         class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center z-20">
        <div class="flex flex-col items-center space-y-2">
            <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-blue-600 font-medium">Loading tickets...</span>
        </div>
    </div>
    <div wire:loading.class="opacity-50" wire:target="search,statusFilter,categoryFilter,priority">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                        @php
                            $priorityColors = [
                                'Low' => 'bg-green-100 text-green-800',
                                'Medium' => 'bg-yellow-100 text-yellow-800',
                                'High' => 'bg-orange-100 text-orange-800',
                                'Critical' => 'bg-red-100 text-red-800',
                            ];
                        @endphp

                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $ticket->priority }}
                            </span>
                        </th>                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ticket->createdBy->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @foreach($ticket->departments as $dept)
                                <span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full mr-1">
                                    {{ $dept->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $ticket->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button wire:click="viewTicket({{ $ticket->id }})"
                                    class="text-blue-600 hover:text-blue-900">View</button>

                                <!-- Status Update Dropdown -->
                                <div class="relative inline-block text-left">
                                    <select wire:change="updateTicketStatus({{ $ticket->id }}, $event.target.value)"
                                        class="text-xs border border-gray-300 rounded px-2 py-1">
                                        <option value="">Update Status</option>
                                        <option value="pending" @if($ticket->status === 'pending') selected @endif>Pending</option>
                                        <option value="in_progress" @if($ticket->status === 'in_progress') selected @endif>In Progress</option>
                                        <option value="resolved" @if($ticket->status === 'resolved') selected @endif>Resolved</option>
                                        <option value="closed" @if($ticket->status === 'closed') selected @endif>Closed</option>
                                    </select>
                                </div>

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
                        <!-- Title Field -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                            <input wire:model.live="title" type="text" placeholder="Enter ticket title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority <span class="text-red-500"></span></label>
                            <select wire:model.live="prioritySelected"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('categoryId') border-red-500 @enderror">
                                <option value="">Select Priority</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>

                            </select>
                            @error('categoryId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>


                        <!-- Category Field -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                            <select wire:model.live="categoryId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('categoryId') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Template Field -->
                        @if($categoryId)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                                <select wire:model.live="templateId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('templateId') border-red-500 @enderror">
                                    <option value="">Select Template</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                                @error('templateId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif
                        <!-- Template Fields -->
                        @if(!empty($templateFields))
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-900 mb-3 border-b pb-2">Template Fields</h4>
                                <div class="space-y-4">
                                    @foreach($templateFields as $field)
                                        <div class="bg-gray-50 p-3 rounded-md">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $field['name'] }}
                                                @if($field['required']) <span class="text-red-500">*</span> @endif
                                            </label>

                                            @if($field['type'] === 'text')
                                                <input wire:model.live="fieldValues.{{ $field['id'] }}" type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fieldValues.' . $field['id']) border-red-500 @enderror"
                                                    placeholder="Enter {{ strtolower($field['name']) }}">

                                            @elseif($field['type'] === 'textarea')
                                                <textarea wire:model.live="fieldValues.{{ $field['id'] }}" rows="3"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fieldValues.' . $field['id']) border-red-500 @enderror"
                                                    placeholder="Enter {{ strtolower($field['name']) }}"></textarea>

                                            @elseif($field['type'] === 'select')
                                                <select wire:model.live="fieldValues.{{ $field['id'] }}"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fieldValues.' . $field['id']) border-red-500 @enderror">
                                                    <option value="">Select {{ $field['name'] }}</option>
                                                    @if(!empty($field['options']))
                                                        @foreach($field['options'] as $option)
                                                            <option value="{{ $option }}">{{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>

                                            @elseif($field['type'] === 'radio')
                                                <div class="space-y-2">
                                                    @if(!empty($field['options']))
                                                        @foreach($field['options'] as $option)
                                                            <label class="flex items-center">
                                                                <input type="radio" wire:model.live="fieldValues.{{ $field['id'] }}" value="{{ $option }}"
                                                                    class="text-blue-600 focus:ring-blue-500">
                                                                <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                                                            </label>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            @elseif($field['type'] === 'checkbox')
                                                <div class="space-y-2">
                                                    @foreach($field['options'] as $option)
                                                        <label class="flex items-center">
                                                            <input type="checkbox"
                                                                wire:model.live="fieldValues.{{ $field['id'] }}"
                                                                value="{{ $option }}"
                                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                            <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @elseif($field['type'] === 'date')
                                                <input wire:model.live="fieldValues.{{ $field['id'] }}" type="date"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fieldValues.' . $field['id']) border-red-500 @enderror">
                                            @elseif($field['type'] === 'datetime')
                                                <input wire:model.live="fieldValues.{{ $field['id'] }}" type="datetime-local"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fieldValues.' . $field['id']) border-red-500 @enderror">
                                            @endif

                                            @error('fieldValues.' . $field['id'])
                                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @php
                            $isFormValid = true;

                            if (empty($title) || strlen(trim($title)) < 3) {
                                $isFormValid = false;
                            }

                            if (empty($categoryId)) {
                                $isFormValid = false;
                            }

                            if (!empty($categoryId) && empty($templateId)) {
                                $isFormValid = false;
                            }

                            if (!empty($templateFields)) {
                                foreach ($templateFields as $field) {
                                    if ($field['required']) {
                                        $fieldValue = $fieldValues[$field['id']] ?? '';

                                        if ($field['type'] === 'checkbox') {
                                            if (empty($fieldValue) || !is_array($fieldValue) || count($fieldValue) === 0) {
                                                $isFormValid = false;
                                                break;
                                            }
                                        } else {
                                            if (empty($fieldValue) || (is_string($fieldValue) && trim($fieldValue) === '')) {
                                                $isFormValid = false;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        @endphp

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 mt-6 pt-4 border-t">
                            <button type="button" wire:click="closeCreateModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Cancel
                            </button>
                            <button type="submit"
                                @if(!$isFormValid) disabled @endif
                                class="px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                @if($isFormValid)
                                    bg-blue-600 text-white hover:bg-blue-700 cursor-pointer
                                @else
                                    bg-gray-400 text-gray-600 cursor-not-allowed
                                @endif">
                                Create Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
