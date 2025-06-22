<?php

namespace App\Livewire\Template;

use App\Models\Template\Template as TemplateTemplate;
use App\Models\Template\TemplateField;
use App\Models\Department;
use App\Models\Template\TemplateFields;
use Livewire\Component;

class Template extends Component
{
    public $categoryId;
    public $name = '';
    public $departmentId = 1;
    public $showCreateForm = false;
    public $showEditModal = false;
    public $showFieldsModal = false;
    public $showFieldForm = false;

    // Template editing
    public $editTemplateId;

    // Field management
    public $selectedTemplateId;
    public $fieldName = '';
    public $fieldType = 'text';
    public $fieldOptions = '';
    public $fieldRequired = false;
    public $fieldOrder = 0;
    public $editFieldId;
    public $showEditFieldModal = false;

    // Field types
    public $fieldTypes = [
        'text' => 'Text Input',
        'textarea' => 'Text Area',
        'select' => 'Select Dropdown',
        'checkbox' => 'Checkbox',
        'radio' => 'Radio Buttons',
        'date' => 'Date Picker',
        'file' => 'File Upload'
    ];

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'departmentId' => 'required|exists:departments,id',
        'fieldName' => 'required|string|min:2|max:255',
        'fieldType' => 'required|in:text,textarea,select,checkbox,radio,date,file',
        'fieldOrder' => 'integer|min:0',
    ];

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function resetForm()
    {
        $this->name = '';
        $this->departmentId = 1;
        $this->showCreateForm = false;
        $this->resetValidation();
    }

    public function resetFieldForm()
    {
        $this->fieldName = '';
        $this->fieldType = 'text';
        $this->fieldOptions = '';
        $this->fieldRequired = false;
        $this->fieldOrder = 0;
        $this->showFieldForm = false;
        $this->editFieldId = null;
        $this->showEditFieldModal = false;
        $this->resetValidation();
    }

    public function createTemplate()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'departmentId' => 'required|exists:departments,id',
        ]);

        TemplateTemplate::create([
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'department_id' => $this->departmentId,
        ]);

        $this->resetForm();
        session()->flash('message', 'Template created successfully!');
    }

    public function editTemplate($id)
    {
        $template = TemplateTemplate::findOrFail($id);
        $this->editTemplateId = $template->id;
        $this->name = $template->name;
        $this->departmentId = $template->department_id;
        $this->showEditModal = true;
    }

    public function updateTemplate()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'departmentId' => 'required|exists:departments,id',
        ]);

        $template = TemplateTemplate::findOrFail($this->editTemplateId);
        $template->update([
            'name' => $this->name,
            'department_id' => $this->departmentId,
        ]);

        session()->flash('message', 'Template updated successfully!');
        $this->reset(['name', 'departmentId', 'editTemplateId', 'showEditModal']);
    }

    public function deleteTemplate(int $id)
    {
        TemplateTemplate::find($id)->delete();
        session()->flash('message', 'Template deleted successfully!');
    }

    // Field Management Methods
    public function showTemplateFields($templateId)
    {
        $this->selectedTemplateId = $templateId;
        $this->showFieldsModal = true;
    }

    public function createField()
    {
        $this->validate([
            'fieldName' => 'required|string|min:2|max:255',
            'fieldType' => 'required|in:text,textarea,select,checkbox,radio,date,file',
            'fieldOrder' => 'integer|min:0',
        ]);

        // Validate options for select/radio/checkbox types
        if (in_array($this->fieldType, ['select', 'radio', 'checkbox']) && empty($this->fieldOptions)) {
            $this->addError('fieldOptions', 'Options are required for this field type.');
            return;
        }

        TemplateFields::create([
            'template_id' => $this->selectedTemplateId,
            'name' => $this->fieldName,
            'type' => $this->fieldType,
            'options' => $this->fieldOptions,
            'required' => $this->fieldRequired,
            'order' => $this->fieldOrder,
        ]);

        $this->resetFieldForm();
        session()->flash('message', 'Field added successfully!');
    }

    public function editField($fieldId)
    {
        $field = TemplateFields::findOrFail($fieldId);
        $this->editFieldId = $field->id;
        $this->fieldName = $field->name;
        $this->fieldType = $field->type;
        $this->fieldOptions = $field->options;
        $this->fieldRequired = $field->required;
        $this->fieldOrder = $field->order;
        $this->showEditFieldModal = true;
    }

    public function updateField()
    {
        $this->validate([
            'fieldName' => 'required|string|min:2|max:255',
            'fieldType' => 'required|in:text,textarea,select,checkbox,radio,date,file',
            'fieldOrder' => 'integer|min:0',
        ]);

        if (in_array($this->fieldType, ['select', 'radio', 'checkbox']) && empty($this->fieldOptions)) {
            $this->addError('fieldOptions', 'Options are required for this field type.');
            return;
        }

        $field = TemplateFields::findOrFail($this->editFieldId);
        $field->update([
            'name' => $this->fieldName,
            'type' => $this->fieldType,
            'options' => $this->fieldOptions,
            'required' => $this->fieldRequired,
            'order' => $this->fieldOrder,
        ]);

        $this->resetFieldForm();
        session()->flash('message', 'Field updated successfully!');
    }

    public function deleteField($fieldId)
    {
        TemplateFields::find($fieldId)->delete();
        session()->flash('message', 'Field deleted successfully!');
    }

    public function render()
    {
        $templates = TemplateTemplate::with(['department', 'fields' => function($query) {
            $query->orderBy('order');
        }])->where('category_id', $this->categoryId)->get();

        $selectedTemplateFields = [];
        if ($this->selectedTemplateId) {
            $selectedTemplateFields = TemplateFields::where('template_id', $this->selectedTemplateId)
                ->orderBy('order')
                ->get();
        }

        return view('livewire.template.template', [
            'templates' => $templates,
            'departments' => Department::all(),
            'selectedTemplateFields' => $selectedTemplateFields,
        ]);
    }
}
