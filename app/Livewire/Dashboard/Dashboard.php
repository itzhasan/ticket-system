<?php

namespace App\Livewire\Dashboard;

use App\Models\Ticket\Ticket;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.dashboard.dashboard', [
            'totalTickets' => Ticket::count(),
            'pendingTickets' => Ticket::where('status', 'pending')->count(),
        ]);
    }
}
