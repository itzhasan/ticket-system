<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <button onclick="history.back()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-bold text-gray-900">Ticket #{{ $selectedTicket->id }} - {{ $selectedTicket->title }}</h1>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($selectedTicket->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($selectedTicket->status === 'in_progress') bg-blue-100 text-blue-800
                        @elseif($selectedTicket->status === 'resolved') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $selectedTicket->status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column - Ticket Details -->
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
                        @foreach($selectedTicket->ticketFieldsValues as $fieldValue)
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ $fieldValue->templateField->name }}</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $fieldValue->value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Messages -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border h-full flex flex-col">
                    <!-- Messages Header -->
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-lg font-medium text-gray-900">Messages</h2>
                    </div>

                    <!-- Messages Container -->
                    <div class="flex-1 px-6 py-4 overflow-y-auto max-h-96 min-h-96">
                        <div class="space-y-4">
                            @if(count($messages) > 0)
                                @foreach($messages as $message)
                                <div class="flex {{ auth()->user()->id == $message['user_id'] ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-xs lg:max-w-md">
                                        <!-- Message bubble -->
                                        <div class="px-4 py-2 rounded-lg {{ auth()->user()->id == $message['user_id'] ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-900' }}">
                                            <p class="text-sm">{{ $message['content'] }}</p>
                                        </div>

                                        <!-- Message info -->
                                        <div class="mt-1 {{ auth()->user()->id == $message['user_id'] ? 'text-right' : 'text-left' }}">
                                            <span class="text-xs text-gray-500">
                                                {{ $message['user']['name'] }} • {{ \Carbon\Carbon::parse($message['created_at'])->format('M d, H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="flex justify-center items-center h-32">
                                    <p class="text-gray-500 text-sm">No messages yet. Start the conversation!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="px-6 py-4 border-t">
                        <form wire:submit.prevent="sendMessage" class="flex space-x-3">
                            <div class="flex-1">
                                <input
                                    wire:model="newMessage"
                                    type="text"
                                    placeholder="Type your message..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required
                                >
                            </div>
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            >
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
