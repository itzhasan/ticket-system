<div>
    @auth
        <nav class="bg-white shadow">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-semibold ml-12">Dashboard</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        @php
                        $department = null;
                        if (auth()->user()->department_id) {
                        $department = \App\Models\Department::find(auth()->user()->department_id);
                        }
                        @endphp
                        <span class="bg-cyan-300 text-blue-800 px-2 py-1 rounded text-sm">
                            {{ $department ? ucfirst($department->name) : 'No Department' }}
                        </span>
                    </div>
                </div>
            </div>
        </nav>


        @endauth
</div>
