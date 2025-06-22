<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class UserManagement extends Component
{
    use WithPagination;

    public $showCreateForm = false;
    public $editingUserId = null;
    protected $listeners = ['closeForm' => 'closeForm'];

    public function closeForm()
    {
        $this->showCreateForm = false;
    }

    #[Title('User Management')]
    public function render()
    {
        return view('livewire.user.user-management', [
            'users' => User::paginate(10),
        ]);
    }
}
