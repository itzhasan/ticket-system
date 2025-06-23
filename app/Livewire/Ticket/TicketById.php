<?php
namespace App\Livewire\Ticket;

use App\Models\Ticket\Ticket;
use App\Models\Message;
use App\Models\Ticket\Message as TicketMessage;
use App\Models\Ticket\TicketDepartment;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TicketById extends Component
{
    public $selectedTicket;
    public $id;
    public $messages = [];
    public $newMessage = '';

    public function mount($id)
    {
        $this->id = $id;
        $this->loadTicket();
        $this->loadMessages();
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
            'ticket_id' => $this->id,
            'content' => $this->newMessage
        ]);

        $this->newMessage = '';
        $this->loadMessages();

        $this->dispatch('message-sent');
    }
    public function forwardTicket($ticketId, $departmentIds)
    {
        TicketDepartment::create([
            'ticket_id' => $ticketId,
            'department_id' => $departmentIds,
        ]);

        session()->flash('message', 'Ticket forwarded successfully!');
    }

    public function render()
    {
        return view('livewire.ticket.ticket-by-id');
    }
}
