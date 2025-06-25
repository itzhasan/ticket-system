<?php

namespace App\Livewire\Dashboard;

use App\Models\Ticket\Ticket;
use App\Services\UserAccessService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    #[Title('Dashboard')]
    public function render()
    {
        $access = UserAccessService::for(Auth::user());
        
        return view('livewire.dashboard.dashboard', [
            'totalTickets'   => $access->getTickets()->count(),
            'pendingTickets' => $access->getTickets()->where('status', 'pending')->count()
        ]);
    }
}
