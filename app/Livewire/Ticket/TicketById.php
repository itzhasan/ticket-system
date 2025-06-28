<?php

namespace App\Livewire\Ticket;

use App\Models\Department;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Message as TicketMessage;
use App\Models\Ticket\TicketDepartment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Livewire;

class TicketById extends Component
{
    public $selectedTicket;
    public $id;
    public $messages = [];
    public $newMessage = '';
    public $currentDepartment;

    public function mount($id)
    {
        $this->id = $id;
        $this->loadTicket();
        $this->loadMessages();

        $lastDepartment = TicketDepartment::where('ticket_id', $this->id)
            ->latest()
            ->first();

        $this->currentDepartment = $lastDepartment ? $lastDepartment->department : null;
    }

    public function loadTicket()
    {
        $this->selectedTicket = Ticket::with([
            'category',
            'createdBy',
            'departments',
            'ticketFieldsValues.templateField'
        ])->findOrFail($this->id);
    }

    public function loadMessages()
    {
        $messages = TicketMessage::where('ticket_id', $this->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->messages = $messages->toArray();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000'
        ]);

        TicketMessage::create([
            'user_id' => Auth::id(),
            'type' => 'user',
            'ticket_id' => $this->id,
            'content' => $this->newMessage
        ]);

        $this->newMessage = '';
        $this->loadMessages();

        $this->dispatch('message-sent');
    }
    public function forwardTicket($departmentId)
    {
        $exists = TicketDepartment::where('ticket_id', $this->id)
            ->where('department_id', $departmentId)
            ->exists();

        if ($exists) {
            session()->flash('error', 'This department is already assigned .');
            return;
        }

        TicketDepartment::create([
            'ticket_id' => $this->id,
            'department_id' => $departmentId,
        ]);

        $department = Department::find($departmentId);

        \App\Models\Ticket\Message::create([
            'user_id' => Auth::id(),
            'type' => 'system',
            'ticket_id' => $this->id,
            'content' => Auth::user()->name . ' assigned ' . $department->name . ' Department successfully.',
        ]);

        session()->flash('message', 'Ticket forwarded successfully!');
        $this->loadMessages();
    }
    public function updateTicketStatus($ticketId, $status)
    {
        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            session()->flash('error', 'Ticket not found.');
            return;
        }

        $allowedStatuses = ['pending', 'in_progress', 'resolved', 'closed'];
        if (!in_array($status, $allowedStatuses)) {
            session()->flash('error', 'Invalid status selected.');
            return;
        }

        $ticket->update(['status' => $status]);

        \App\Models\Ticket\Message::create([
            'user_id' => Auth::id(),
            'type' => 'system',
            'ticket_id' => $this->id,
            'content' => Auth::user()->name . ' has changed the status to ' . $status
        ]);
        $this->loadMessages();
        session()->flash('message', 'Ticket status updated successfully!');
    }
    public function updateTicketPriority($ticketId, $priority)
    {
        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            session()->flash('error', 'Ticket not found.');
            return;
        }

        $ticket->update(['priority' => $priority]);

        \App\Models\Ticket\Message::create([
            'user_id' => Auth::id(),
            'type' => 'system',
            'ticket_id' => $this->id,
            'content' => Auth::user()->name . ' has changed the priority to ' . $priority ,
        ]);
        $this->loadMessages();
        session()->flash('message', 'Ticket priority updated successfully!');
    }


    #[Title('Ticket Details')]
    public function render()
    {
        return view('livewire.ticket.ticket-by-id', [
            'departments' => Department::all(),
        ]);
    }
}
