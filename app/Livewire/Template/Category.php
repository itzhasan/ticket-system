<?php

namespace App\Livewire\Template;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Template\Category as CategoryModel;

class Category extends Component
{
    public $name = '';
    public $editCategoryId;
    public $showEditModal = false;
    public $showCreateForm = false;

    protected $rules = [
        'name' => 'required|string|min:3',
    ];

    public function setCategoryId($id)
    {
        return redirect()->route('admin.templates', ['categoryId' => $id]);
    }

    public function createCategory()
    {
        $this->validate();

        CategoryModel::create([
            'name' => $this->name
        ]);

        $this->resetForm();
        session()->flash('message', 'Category created successfully!');
    }

    public function deleteCategory(int $id)
    {
        CategoryModel::find($id)->delete();
        session()->flash('message', 'Category deleted successfully!');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->closeForm();
        $this->resetValidation();
    }

    public function closeForm()
    {
        $this->showCreateForm = false;
    }

    public function editCategory($id)
    {
        $category = CategoryModel::findOrFail($id);
        $this->editCategoryId = $category->id;
        $this->name = $category->name;
        $this->showEditModal = true;
    }

    public function updateCategory()
    {
        $this->validate();

        $category = CategoryModel::findOrFail($this->editCategoryId);
        $category->update(['name' => $this->name]);

        session()->flash('message', 'Category Updated Successfully.');
        $this->reset(['name', 'editCategoryId', 'showEditModal']);
    }

    #[Title('Categories management')]
    public function render()
    {
        return view('livewire.template.category', [
            'categories' => CategoryModel::all(),
        ]);
    }
}
