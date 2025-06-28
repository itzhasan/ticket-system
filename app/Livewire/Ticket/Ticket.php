<?php

namespace App\Livewire\Ticket;

use App\Models\Department;
use App\Models\Ticket\TicketDepartment;
use App\Models\Ticket\TicketFieldsValue;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Template\Template as TemplateTemplate;
use App\Models\Ticket\Ticket as TicketTicket;
use App\Services\UserAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;

class Ticket extends Component
{
    use WithPagination;

    public $showCreateModal = false;

    // Ticket properties
    public $selectedTicket;
    public $title;
    public $categoryId;
    public $templateId;
    public $departmentIds;
    public $templateFields = [];
    public $fieldValues = [];
    public $prioritySelected;

    // Filter properties
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    public $search = '';
    public $template;

    public function getAccessProperty()
    {
        return UserAccessService::for(Auth::user());
    }

    public $priorityOptions = [
        1 => 'Low',
        2 => 'Medium',
        3 => 'High',
        4 => 'Critical'
    ];

    public $priority = 'all';
    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'categoryId' => 'required|exists:categories,id',
        'templateId' => 'nullable|exists:templates,id',
        'departmentIds' => 'required|min:1',
        'departmentIds.*' => 'exists:departments,id',
        'selectedTicket.status' => 'in:pending,in_progress,resolved,closed',
    ];

    public function mount()
    {
        $this->resetForm();
        $this->template = TemplateTemplate::where('id',$this->templateId);
    }

    #[Title('Tickets')]
    public function render()
    {
        //there is a game in access ;)
        $query = $this->access->getTickets()->with(['category', 'createdBy', 'departments']);

        $query = $this->search($query);
        $query = $this->filters($query);

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        $categories = $this->access->getCategories();
        $departments = Department::all();
        $templates = $this->access->getTemplates()->with(['templateFields.fieldOptions'])
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->get();

        return view('livewire.ticket.ticket', [
            'tickets' => $tickets,
            'categories' => $categories,
            'departments' => $departments,
            'templates' => $templates,
            'priorityOptions' => $this->priorityOptions,
        ]);
    }

    private function search($query)
    {
        return $query->when($this->search, function ($q) {
            $searchTerm = $this->search;
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('id', 'like', $searchTerm . '%')
                    ->orWhereHas('category', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('messages', function ($q3) use ($searchTerm) {
                        $q3->where('content', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('user', function ($q4) use ($searchTerm) {
                        $q4->where('name', 'like', '%' . $searchTerm . '%');
                    })->orWhereHas('ticketFieldsValues', function ($q5) use ($searchTerm) {
                        $q5->where('value', 'like', '%' . $searchTerm . '%');
                    });
            });
        });
    }

    private function filters($query)
    {
        return $query->when($this->statusFilter !== 'all', function ($q) {
            $q->where('status', $this->statusFilter);
        })
            ->when($this->categoryFilter !== 'all', function ($q) {
                $q->where('category_id', $this->categoryFilter);
            })
            ->when($this->priority !== 'all', function ($q) {
                $q->where('priority', $this->priority);
            });
    }

    public function updatedCategoryId()
    {
        $this->templateId = null;
        $this->templateFields = [];
        $this->fieldValues = [];
        $this->departmentIds = null;
    }

    public function updatedTemplateId()
    {
        if ($this->templateId) {
            $template = TemplateTemplate::with(['templateFields.fieldOptions'])->find($this->templateId);
            if ($template) {
                $this->templateFields = $template->templateFields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'name' => $field->name,
                        'type' => $field->type,
                        'required' => $field->required,
                        'order' => $field->order,
                        'options' => $field->fieldOptions->pluck('value')->toArray()
                    ];
                })->toArray();

                foreach ($this->templateFields as $field) {
                    $this->fieldValues[$field['id']] = '';
                    if ($field['type'] == 'checkbox') {
                        $this->fieldValues[$field['id']] = [];
                    }
                }

                $this->departmentIds = $template->department_id;
            }
        } else {
            $this->templateFields = [];
            $this->fieldValues = [];
            $this->departmentIds = null;
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createTicket()
    {
        $this->validate([
            'title' => 'required|string|min:3|max:255',
            'categoryId' => 'required|exists:categories,id',
            'templateId' => 'nullable|exists:templates,id',
        ]);

        if ($this->templateId && !empty($this->templateFields)) {
            foreach ($this->templateFields as $field) {
                $fieldValue = $this->fieldValues[$field['id']] ?? '';

                if ($field['required']) {
                    if ($field['type'] === 'checkbox') {
                        if (empty($fieldValue) || !is_array($fieldValue) || count($fieldValue) === 0) {
                            $this->addError('fieldValues.' . $field['id'], 'This field is required.');
                        }
                    } else {
                        if (empty($fieldValue) || (is_string($fieldValue) && trim($fieldValue) === '')) {
                            $this->addError('fieldValues.' . $field['id'], 'This field is required.');
                        }
                    }
                }
            }

            if ($this->getErrorBag()->has('fieldValues.*')) {
                return;
            }
        }

        try {
            DB::beginTransaction();

            $ticket = TicketTicket::create([
                'title' => $this->title,
                'priority' => $this->prioritySelected ?? 'Low',
                'category_id' => $this->categoryId,
                'created_by_id' => Auth::id(),
                'status' => 'pending',
            ]);

            if ($this->templateId && !empty($this->templateFields)) {
                foreach ($this->fieldValues as $fieldId => $value) {
                    if (is_array($value)) {
                        if (!empty($value)) {
                            foreach ($value as $selectedOption) {
                                TicketFieldsValue::create([
                                    'ticket_id' => $ticket->id,
                                    'template_field_id' => $fieldId,
                                    'value' => $selectedOption,
                                ]);
                            }
                        }
                    } else {
                        if (!empty($value) && trim($value) !== '') {
                            TicketFieldsValue::create([
                                'ticket_id' => $ticket->id,
                                'template_field_id' => $fieldId,
                                'value' => $value,
                            ]);
                        }
                    }
                }
            }
            $templates = $this->access->getTemplates()->with(['templateFields.fieldOptions'])
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->get();

            TicketDepartment::create([
                'ticket_id' => $ticket->id,
                'department_id' => $this->template->department_id,
            ]);


            DB::commit();

            session()->flash('message', 'Ticket created successfully!');
            $this->resetForm();
            $this->closeCreateModal();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create ticket. Please try again.');
        }
    }
    public function viewTicket($ticketId)
    {
        return redirect()->route('ticket.view', ['id' => $ticketId]);
    }

    public function updateTicketStatus($ticketId, $status)
    {
        $ticket = TicketTicket::find($ticketId);
        if ($ticket) {
            $ticket->update(['status' => $status]);
            session()->flash('message', 'Ticket status updated successfully!');
        }
    }

    public function deleteTicket($ticketId)
    {
        $ticket = TicketTicket::find($ticketId);
        if ($ticket) {
            $ticket->delete();
            session()->flash('message', 'Ticket deleted successfully!');
            $this->resetPage();
        }
    }

    private function resetForm()
    {
        $this->title = '';
        $this->categoryId = null;
        $this->templateId = null;
        $this->departmentIds;
        $this->templateFields = [];
        $this->fieldValues = [];
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function getTicketsByStatus($status)
    {
        return TicketTicket::where('status', $status)
            ->with(['category', 'createdBy', 'departments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTicketsByDepartment($departmentId)
    {
        return TicketTicket::whereHas('departments', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->with(['category', 'createdBy', 'departments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
