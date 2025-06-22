<?php

namespace App\Livewire\Auth;

use App\Livewire\User\UserManagement;
use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'user';
    public $departmentId = 1;
    public $editingUserId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required|in:admin,user',
    ];

    public function createUser()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'department_id' => $this->departmentId,
        ]);

        $this->resetForm();
        session()->flash('message', 'User created successfully!');
    }

    public function deleteUser($userId)
    {
        if ($userId !== Auth::user()->id) {
            User::find($userId)->delete();
            session()->flash('message', 'User deleted successfully!');
        }
    }
    public function closeForm()
    {
        $this->dispatch('closeForm');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'user';
        $this->editingUserId = null;
        $this->closeForm();
        $this->resetValidation();
    }
    public function render()
    {
        return view('livewire.auth.register', [
            'departments' => Department::all(),
        ]);
    }
}
