<?php

namespace App\Livewire\Template;

use App\Models\Template\FieldOption;
use App\Models\Template\Template as TemplateTemplate;
use App\Models\Template\TemplateField;
use App\Models\Department;
use App\Models\Template\TemplateFields;
use Livewire\Attributes\Title;
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
    public $fieldOptions = []; // Changed from string to array
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
        'fieldOptions.*' => 'nullable|string|max:255', // Validate each option
    ];

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->fieldOptions = ['']; // Initialize with one empty option
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
        $this->fieldOptions = ['']; // Reset to single empty option
        $this->fieldRequired = false;
        $this->fieldOrder = 0;
        $this->showFieldForm = false;
        $this->editFieldId = null;
        $this->showEditFieldModal = false;
        $this->resetValidation();
    }

    // Add new option input
    public function addOption()
    {
        $this->fieldOptions[] = '';
    }

    // Remove option input
    public function removeOption($index)
    {
        if (count($this->fieldOptions) > 1) {
            unset($this->fieldOptions[$index]);
            $this->fieldOptions = array_values($this->fieldOptions); // Re-index array
        }
    }

    // Update field type and reset options if needed
    public function updatedFieldType()
    {
        if (!in_array($this->fieldType, ['select', 'radio', 'checkbox'])) {
            $this->fieldOptions = [''];
        } elseif (empty($this->fieldOptions) || (count($this->fieldOptions) == 1 && empty($this->fieldOptions[0]))) {
            $this->fieldOptions = [''];
        }
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
            'fieldOptions.*' => 'nullable|string|max:255',
        ]);

        // Validate options for select/radio/checkbox types
        if (in_array($this->fieldType, ['select', 'radio', 'checkbox'])) {
            $filteredOptions = array_filter($this->fieldOptions, function($option) {
                return !empty(trim($option));
            });

            if (empty($filteredOptions)) {
                $this->addError('fieldOptions', 'At least one option is required for this field type.');
                return;
            }
        }

        // Create the template field
        $templateField = TemplateFields::create([
            'template_id' => $this->selectedTemplateId,
            'name' => $this->fieldName,
            'type' => $this->fieldType,
            'required' => $this->fieldRequired,
            'order' => $this->fieldOrder,
        ]);

        // Create field options if the field type requires them
        if (in_array($this->fieldType, ['select', 'radio', 'checkbox'])) {
            $filteredOptions = array_filter($this->fieldOptions, function($option) {
                return !empty(trim($option));
            });

            foreach ($filteredOptions as $option) {
                FieldOption::create([
                    'template_field_id' => $templateField->id,
                    'value' => trim($option),
                ]);
            }
        }

        $this->resetFieldForm();
        session()->flash('message', 'Field added successfully!');
    }

    public function editField($fieldId)
    {
        $field = TemplateFields::with('fieldOptions')->findOrFail($fieldId);
        $this->editFieldId = $field->id;
        $this->fieldName = $field->name;
        $this->fieldType = $field->type;
        $this->fieldRequired = $field->required;
        $this->fieldOrder = $field->order;

        // Convert field options to array format
        if ($field->fieldOptions->isNotEmpty()) {
            $this->fieldOptions = $field->fieldOptions->pluck('value')->toArray();
        } else {
            $this->fieldOptions = [''];
        }

        $this->showEditFieldModal = true;
    }

    public function updateField()
    {
        $this->validate([
            'fieldName' => 'required|string|min:2|max:255',
            'fieldType' => 'required|in:text,textarea,select,checkbox,radio,date,file',
            'fieldOrder' => 'integer|min:0',
            'fieldOptions.*' => 'nullable|string|max:255',
        ]);

        if (in_array($this->fieldType, ['select', 'radio', 'checkbox'])) {
            $filteredOptions = array_filter($this->fieldOptions, function($option) {
                return !empty(trim($option));
            });

            if (empty($filteredOptions)) {
                $this->addError('fieldOptions', 'At least one option is required for this field type.');
                return;
            }
        }

        $field = TemplateFields::findOrFail($this->editFieldId);

        // Update the template field
        $field->update([
            'name' => $this->fieldName,
            'type' => $this->fieldType,
            'required' => $this->fieldRequired,
            'order' => $this->fieldOrder,
        ]);

        // Delete existing options
        FieldOption::where('template_field_id', $field->id)->delete();

        // Create new field options if the field type requires them
        if (in_array($this->fieldType, ['select', 'radio', 'checkbox'])) {
            $filteredOptions = array_filter($this->fieldOptions, function($option) {
                return !empty(trim($option));
            });

            foreach ($filteredOptions as $option) {
                FieldOption::create([
                    'template_field_id' => $field->id,
                    'value' => trim($option),
                ]);
            }
        }

        $this->resetFieldForm();
        session()->flash('message', 'Field updated successfully!');
    }

    public function deleteField($fieldId)
    {
        $field = TemplateFields::findOrFail($fieldId);

        // Delete associated field options first (if using cascade, this might be automatic)
        FieldOption::where('template_field_id', $fieldId)->delete();

        // Delete the field
        $field->delete();

        session()->flash('message', 'Field deleted successfully!');
    }

    // Add this property to load template fields with their options
    public function getSelectedTemplateFieldsProperty()
    {
        if (!$this->selectedTemplateId) {
            return collect();
        }

        return TemplateFields::with('fieldOptions')
            ->where('template_id', $this->selectedTemplateId)
            ->orderBy('order')
            ->get();
    }

    #[Title('Template management')]
    public function render()
    {
        $templates = TemplateTemplate::with(['department', 'fields' => function ($query) {
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
