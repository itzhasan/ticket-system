<?php

namespace App\Livewire\User;

use App\Models\Department as ModelsDepartment;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Department extends Component
{
    use WithPagination;

    public $showCreateForm = false;
    public $showEditModal = false;
    public $name = '';
    public $departments = [];
    public $currentDepartmentId = null;
    public $editingDepartment = null;

    public function mount()
    {
        $this->departments = ModelsDepartment::all();
    }

    public function openEditModal($id)
    {
        try {
            $this->currentDepartmentId = $id;
            $this->editingDepartment = ModelsDepartment::findOrFail($id);
            $this->name = $this->editingDepartment->name;
            $this->showEditModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Department not found.');
        }
    }

    public function createDepartment()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        ModelsDepartment::create(['name' => $this->name]);
        $this->resetForm();
        session()->flash('message', 'Department created successfully!');
        $this->refreshDepartments();
    }

    public function updateDepartment()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        if ($this->currentDepartmentId) {
            $department = ModelsDepartment::findOrFail($this->currentDepartmentId);
            $department->update(['name' => $this->name]);

            session()->flash('message', 'Department updated successfully!');
            $this->resetForm();
            $this->refreshDepartments();
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->currentDepartmentId = null;
        $this->editingDepartment = null;
        $this->closeForm();
        $this->closeEditModal();
        $this->resetValidation();
    }

    public function closeForm()
    {
        $this->showCreateForm = false;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->currentDepartmentId = null;
        $this->editingDepartment = null;
        $this->name = '';
        $this->resetValidation();
    }

    public function refreshDepartments()
    {
        $this->departments = ModelsDepartment::all();
    }

    #[Title('Department Management')]
    public function render()
    {
        return view('livewire.user.department', [
            'departments' => $this->departments,
        ]);
    }
}
