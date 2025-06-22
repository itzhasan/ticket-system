<div class="mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h1>

            @if(auth()->user()->isAdmin())
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <h2 class="text-lg font-semibold text-blue-900 mb-2">Admin Panel</h2>
                    <p class="text-blue-700 mb-4">Welcome to the admin dashboard. You have full access to manage users.</p>
                    <a href="{{ route('admin.users') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Manage Users
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Your Role</h3>
                    <p class="text-gray-600">{{ ucfirst(auth()->user()->role) }}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Account Info</h3>
                    <p class="text-gray-600">{{ auth()->user()->name }}</p>
                    <p class="text-gray-500 text-sm">{{ auth()->user()->email }}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Member Since</h3>
                    <p class="text-gray-600">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
