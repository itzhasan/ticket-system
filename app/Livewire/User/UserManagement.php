<?php

namespace App\Livewire\User;

use App\Models\Department;
use App\Models\Template\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class UserManagement extends Component
{
    use WithPagination;

    public $showCreateForm = false;
    public $editingUserId = null;
    public $showCategoryModal = false;
    public $selectedUserId = null;
    public $selectedCategories = [];
    public $departments = [];

    protected $listeners = ['closeForm' => 'closeForm'];

    public function mount()
    {
        $this->departments = Department::all();
    }
    public function closeForm()
    {
        $this->showCreateForm = false;
    }

    public function openCategoryModal($userId)
    {
        $this->selectedUserId = $userId;
        $user = User::find($userId);
        $this->selectedCategories = $user->categories->pluck('id')->toArray();
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->selectedUserId = null;
        $this->selectedCategories = [];
    }

    public function updateUserCategories()
    {
        $this->validate([
            'selectedCategories' => 'array',
            'selectedCategories.*' => 'exists:categories,id'
        ]);

        $user = User::find($this->selectedUserId);
        $user->categories()->sync($this->selectedCategories);

        session()->flash('message', 'User categories updated successfully!');
        $this->closeCategoryModal();
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== Auth::user()->id) {
            $user->delete();
            session()->flash('message', 'User deleted successfully!');
        }
    }
    public function updateDepartment($departmentId, $userId){
        try {
            $user = User::find($userId);
            if ($user) {
                $user->department_id = $departmentId;
                $user->save();
                session()->flash('message', 'User department updated successfully!');
            } else {
                session()->flash('error', 'User not found.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user department: ' . $e->getMessage());
        }
    }

    #[Title('User Management')]
    public function render()
    {
        return view('livewire.user.user-management', [
            'users' => User::with(['department', 'categories'])->paginate(10),
            'allCategories' => Category::all(),
        ]);
    }
}
