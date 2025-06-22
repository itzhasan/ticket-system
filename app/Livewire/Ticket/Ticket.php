<?php

namespace App\Livewire\Ticket;

use App\Models\Department;
use App\Models\Template\Category;
use App\Models\Ticket\TicketDepartment;
use App\Models\Ticket\TicketFieldsValue;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Template\Template as TemplateTemplate;
use App\Models\Ticket\Message as TicketMessage;
use App\Models\Ticket\Ticket as TicketTicket;
use Illuminate\Support\Facades\Auth;

class Ticket extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showViewModal = false;
    public $showMessageModal = false;

    // Ticket properties
    public $selectedTicket;
    public $title;
    public $categoryId;
    public $templateId;
    public $departmentIds;
    public $templateFields = [];
    public $fieldValues = [];

    // Message properties
    public $messageContent;
    public $messages = [];

    // Filter properties
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'categoryId' => 'required|exists:categories,id',
        'templateId' => 'required|exists:templates,id',
        'departmentIds' => 'required|min:1',
        'departmentIds.*' => 'exists:departments,id',
        'messageContent' => 'required|string|min:1',
        'selectedTicket.status' => 'in:pending,in_progress,resolved,closed',
    ];
    // protected $messages = [
    //     'title.required' => 'The ticket title is required.',
    //     'categoryId.required' => 'Please select a category.',
    //     'templateId.required' => 'Please select a template.',
    //     'departmentIds.required' => 'Please select at least one department.',
    //     'departmentIds.min' => 'Please select at least one department.',
    //     'messageContent.required' => 'Message content is required.',
    //     'messageContent.min' => 'Message must be at least 1 character long.',
    // ];

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $tickets = TicketTicket::with(['category', 'createdBy', 'departments'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter !== 'all', function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::all();
        $departments = Department::all();
        $templates = TemplateTemplate::with('templateFields')
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->get();

        return view('livewire.ticket.ticket', [
            'tickets' => $tickets,
            'categories' => $categories,
            'departments' => $departments,
            'templates' => $templates,
        ]);
    }

    public function updatedCategoryId()
    {
        $this->templateId = null;
        $this->templateFields = [];
        $this->fieldValues = [];
    }

    public function updatedTemplateId()
    {
        if ($this->templateId) {
            $template = TemplateTemplate::with('templateFields')->find($this->templateId);
            $this->templateFields = $template->templateFields->toArray();

            // Initialize field values
            foreach ($this->templateFields as $field) {
                $this->fieldValues[$field['id']] = '';
            }
        } else {
            $this->templateFields = [];
            $this->fieldValues = [];
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
        'title' => 'required|string|min:3',
        'categoryId' => 'required|integer',
        'templateId' => 'nullable|integer',
        'departmentIds' => 'required|integer', // Single select, must be integer
    ]);

    // Validate required template fields
    foreach ($this->templateFields as $field) {
        if ($field['required'] && empty($this->fieldValues[$field['id']])) {
            $this->addError('fieldValues.' . $field['id'], 'This field is required.');
            return; // stops the function if required field is missing
        }
    }

    // Create ticket
    $ticket = TicketTicket::create([
        'title' => $this->title,
        'category_id' => $this->categoryId,
        'created_by_id' => Auth::id(),
        'status' => 'pending',
    ]);

    // Save template field values
    foreach ($this->fieldValues as $fieldId => $value) {
        if (!empty($value)) {
            TicketFieldsValue::create([
                'ticket_id' => $ticket->id,
                'template_field_id' => $fieldId,
                'value' => $value,
            ]);
        }
    }

    // Assign single department
    TicketDepartment::create([
        'ticket_id' => $ticket->id,
        'department_id' => $this->departmentIds, // single department id
    ]);

    session()->flash('message', 'Ticket created successfully!');

    // Optional: Reset form values
    $this->reset(['title', 'categoryId', 'templateId', 'departmentIds', 'fieldValues']);

    // Close modal and reset pagination if needed
    $this->closeCreateModal();
    $this->resetPage();
}


    public function viewTicket($ticketId)
    {
        $this->selectedTicket = TicketTicket::with([
            'category',
            'createdBy',
            'departments',
            'ticketFieldsValues.templateField',
            'messages.user'
        ])->find($ticketId);

        $this->messages = $this->selectedTicket->messages()->with('user')->orderBy('created_at', 'asc')->get();
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedTicket = null;
        $this->messages = [];
    }

    public function openMessageModal($ticketId)
    {
        $this->selectedTicket = TicketTicket::find($ticketId);
        $this->messages = $this->selectedTicket->messages()->with('user')->orderBy('created_at', 'asc')->get();
        $this->showMessageModal = true;
    }

    public function closeMessageModal()
    {
        $this->showMessageModal = false;
        $this->selectedTicket = null;
        $this->messages = [];
        $this->messageContent = '';
    }

    public function sendMessage()
    {
        $this->validate(['messageContent' => 'required|string|min:1']);

        TicketMessage::create([
            'ticket_id' => $this->selectedTicket->id,
            'user_id' => Auth::id(),
            'content' => $this->messageContent,
        ]);

        $this->messageContent = '';
        $this->messages = $this->selectedTicket->messages()->with('user')->orderBy('created_at', 'asc')->get();

        session()->flash('message', 'Message sent successfully!');
    }

    public function updateTicketStatus($ticketId, $status)
    {
        $ticket = TicketTicket::find($ticketId);
        $ticket->update(['status' => $status]);

        session()->flash('message', 'Ticket status updated successfully!');
    }

    public function deleteTicket($ticketId)
    {
        $ticket = TicketTicket::find($ticketId);
        $ticket->delete();

        session()->flash('message', 'Ticket deleted successfully!');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->categoryId = null;
        $this->templateId = null;
        $this->departmentIds;
        $this->templateFields = [];
        $this->fieldValues = [];
        $this->messageContent = '';
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
    public function refreshMessages()
    {
        if ($this->selectedTicket) {
            $this->messages = $this->selectedTicket->messages()->with('user')->orderBy('created_at', 'asc')->get();
        }
    }
    public function forwardTicket($ticketId, $departmentIds)
    {
        $ticket = TicketTicket::find($ticketId);

        // Remove existing department assignments
        TicketDepartment::where('ticket_id', $ticketId)->delete();

        // Add new department assignments
        TicketDepartment::create([
            'ticket_id' => $ticketId,
            'department_id' => $departmentIds,
        ]);

        session()->flash('message', 'Ticket forwarded successfully!');
    }

    public function getTicketsByStatus($status)
    {
        return Ticket::where('status', $status)
            ->with(['category', 'createdBy', 'departments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTicketsByDepartment($departmentId)
    {
        return Ticket::whereHas('departments', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->with(['category', 'createdBy', 'departments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function bulkAction($action, $ticketIds)
    {
        switch ($action) {
            case 'delete':
                Ticket::whereIn('id', $ticketIds)->delete();
                session()->flash('message', 'Selected tickets deleted successfully!');
                break;

            case 'mark_resolved':
                Ticket::whereIn('id', $ticketIds)->update(['status' => 'resolved']);
                session()->flash('message', 'Selected tickets marked as resolved!');
                break;

            case 'mark_closed':
                Ticket::whereIn('id', $ticketIds)->update(['status' => 'closed']);
                session()->flash('message', 'Selected tickets marked as closed!');
                break;

            case 'mark_pending':
                Ticket::whereIn('id', $ticketIds)->update(['status' => 'pending']);
                session()->flash('message', 'Selected tickets marked as pending!');
                break;

            case 'mark_in_progress':
                Ticket::whereIn('id', $ticketIds)->update(['status' => 'in_progress']);
                session()->flash('message', 'Selected tickets marked as in progress!');
                break;
        }

        $this->resetPage();
    }

    // Method to get ticket statistics
    public function getTicketStats()
    {
        return [
            'total' => Ticket::count(),
            'pending' => Ticket::where('status', 'pending')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];
    }

    public function exportTickets()
    {
        $tickets = Ticket::with(['category', 'createdBy', 'departments', 'ticketFieldsValues.templateField'])
            ->get();

        $csvData = [];
        $csvData[] = ['ID', 'Title', 'Category', 'Status', 'Created By', 'Departments', 'Created At'];

        foreach ($tickets as $ticket) {
            $departments = $ticket->departments->pluck('name')->implode(', ');
            $csvData[] = [
                $ticket->id,
                $ticket->title,
                $ticket->category->name,
                $ticket->status,
                $ticket->createdBy->name,
                $departments,
                $ticket->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'tickets_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return response()->streamDownload(function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, $filename);
    }
}
