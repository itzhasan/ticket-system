<div class="bg-gray-50 p-6 rounded-lg mb-6">
    <h2 class="text-lg font-semibold mb-4">Create New User</h2>
    <form wire:submit.prevent="createUser" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input
                    type="text"
                    wire:model="name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    type="email"
                    wire:model="email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @if ($errors->has('email'))
                <span class="text-red-500 text-sm">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input
                    type="password"
                    wire:model="password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @if ($errors->has('password'))
                <span class="text-red-500 text-sm">{{ $errors->first('password') }}</span>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select
                    wire:model="role"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Department</label>
                <select
                    wire:model="departmentId"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex space-x-2">
            <button
                type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                wire:loading.attr="disabled">
                Create User
            </button>
            <button
                type="button"
                wire:click="resetForm"
                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Cancel
            </button>
        </div>
    </form>
</div>
