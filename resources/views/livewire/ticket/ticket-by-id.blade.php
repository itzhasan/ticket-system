<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <button onclick="history.back()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-900">Ticket #{{ $selectedTicket->id }} -
                        {{ $selectedTicket->title }}
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-64 relative" x-data="{ open: false }">
                        <label>Assign department</label>
                        <!-- Current Department Display -->
                        <button @click="open = !open"
                            class="w-full bg-white border border-gray-300 px-4 py-2 rounded-md shadow-sm flex justify-between items-center">
                            <span>
                                @if($currentDepartment)
                                    {{ $currentDepartment->name }}
                                @else
                                    Assign Department
                                @endif
                            </span>
                            <svg class="w-4 h-4 transform" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Options -->
                        <div x-show="open" @click.away="open = false"
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            @foreach($departments as $department)
                                <div class="flex justify-between items-center px-4 py-2 hover:bg-gray-100">
                                    <span>{{ $department->name }}</span>
                                    <button wire:click="forwardTicket({{ $department->id }})"
                                        class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                                        Assign
                                    </button>
                                </div>
                            @endforeach
                            @if (session()->has('error'))
                                <div class="mt-2 text-red-600 text-sm p-2">
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>


                    </div>
                    <div class="w-64">
                        <label class="">Status</label>
                        <select wire:change="updateTicketStatus({{ $selectedTicket->id }}, $event.target.value)"
                            id="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending" {{ $selectedTicket->status === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="in_progress" {{ $selectedTicket->status === 'in_progress' ? 'selected' : '' }}>
                                In Progress</option>
                            <option value="resolved" {{ $selectedTicket->status === 'resolved' ? 'selected' : '' }}>
                                Resolved</option>
                            <option value="closed" {{ $selectedTicket->status === 'closed' ? 'selected' : '' }}>Closed
                            </option>
                        </select>
                    </div>
                    <div class="w-64">
                        <label class="">Priority</label>
                        <select wire:change="updateTicketPriority({{ $selectedTicket->id }}, $event.target.value)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Low" {{ $selectedTicket->priority === 'Low' ? 'selected' : '' }}>Low
                            </option>
                            <option value="Medium" {{ $selectedTicket->priority === 'Medium' ? 'selected' : '' }}>
                                Medium</option>
                            <option value="High" {{ $selectedTicket->priority === 'High' ? 'selected' : '' }}>
                                High</option>
                            <option value="Critical" {{ $selectedTicket->priority === 'Critical' ? 'selected' : '' }}>Critical
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Ticket Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Category</label>
                            <p class="text-sm text-gray-900">{{ $selectedTicket->category->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Created by</label>
                            <p class="text-sm text-gray-900">{{ $selectedTicket->createdBy->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Created at</label>
                            <p class="text-sm text-gray-900">{{ $selectedTicket->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500">Departments</label>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($selectedTicket->departments as $dept)
                                    <span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">
                                        {{ $dept->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Field Values -->
                @if($selectedTicket->ticketFieldsValues->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Ticket Details</h2>

                        <div class="space-y-3">
                            @foreach($selectedTicket->ticketFieldsValues->groupBy('template_field_id') as $fieldId => $fieldValuesGroup)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">
                                        {{ $fieldValuesGroup->first()->templateField->name }}
                                    </label>

                                    @if($fieldValuesGroup->count() > 1)
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            @foreach($fieldValuesGroup as $value)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $value->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-900 mt-1">
                                            {{ $fieldValuesGroup->first()->value }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border h-full flex flex-col">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-lg font-medium text-gray-900">Messages</h2>
                    </div>
                    <div
                        class="flex-1 px-4 py-6 overflow-y-auto max-h-96 min-h-96 bg-gradient-to-b from-gray-50 to-white">
                        <div class="space-y-6">
                            @if(count($messages) > 0)
                                @foreach($messages as $message)
                                        <div class="flex
                                    @if($message['type'] === 'system') justify-center
                                    @else {{ auth()->user()->id == $message['user_id'] ? 'justify-end' : 'justify-start' }}
                                    @endif">
                                            <div class="max-w-xs lg:max-w-md xl:max-w-lg">
                                                <div class="group relative">
                                                    <div class="px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md
                                                @if($message['type'] === 'system')
                                                    bg-amber-100 border border-amber-200 text-amber-800 text-center mx-auto max-w-fit
                                                @else
                                                    {{ auth()->user()->id == $message['user_id']
                                                    ? 'bg-blue-500 text-white shadow-blue-100 hover:bg-blue-600'
                                                    : 'bg-white border border-gray-200 text-gray-900 hover:border-gray-300' }}
                                                @endif">

                                                        @if($message['type'] === 'system')
                                                            <div class="flex items-center justify-center space-x-2">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                                <p class="text-sm font-medium">{{ $message['content'] }}</p>
                                                            </div>
                                                        @else
                                                            <p class="text-sm leading-relaxed whitespace-pre-wrap">
                                                                {{ $message['content'] }}</p>
                                                        @endif
                                                    </div>
                                                    @if($message['type'] !== 'system')
                                                                                <div class="absolute top-4
                                                                                {{ auth()->user()->id == $message['user_id'] ? '-right-1' : '-left-1' }}">
                                                                                    <div class="w-3 h-3 transform rotate-45
                                                                                    {{ auth()->user()->id == $message['user_id']
                                                        ? 'bg-blue-500'
                                                        : 'bg-white border-l border-b border-gray-200' }}">
                                                                                    </div>
                                                                                </div>
                                                    @endif
                                                </div>

                                                @if($message['type'] !== 'system')
                                                    <div
                                                        class="mt-2 px-2 {{ auth()->user()->id == $message['user_id'] ? 'text-right' : 'text-left' }}">
                                                        <div
                                                            class="flex items-center space-x-2 {{ auth()->user()->id == $message['user_id'] ? 'justify-end' : 'justify-start' }}">
                                                            <span class="text-xs text-gray-500
                                                            {{ auth()->user()->id == $message['user_id'] ? 'order-1' : 'order-2' }}">
                                                                <span class="font-medium">{{ $message['user']['name'] }}</span>
                                                                <span class="mx-1">•</span>
                                                                <span>{{ \Carbon\Carbon::parse($message['created_at'])->format('M d, H:i') }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                @endforeach
                            @else
                                <div class="flex flex-col justify-center items-center h-32 space-y-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 text-sm font-medium">No messages yet</p>
                                        <p class="text-gray-400 text-xs mt-1">Start the conversation to begin chatting!</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t">
                    <form wire:submit.prevent="sendMessage" class="flex space-x-3">
                        <div class="flex-1">
                            <input wire:model="newMessage" type="text" placeholder="Type your message..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                        </div>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
