<?php

namespace App\Services;

use App\Models\Template\Category;
use App\Models\Template\Template;
use App\Models\Template\UserCategory;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketDepartment;
use App\Models\User;


class UserAccessService
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user->load('categories');
    }

    public static function for(User $user): static
    {
        return new static($user);
    }

    public function getAllowedCategoryIds(): array
    {
        if ($this->user->role !== 'user') {
            return Category::pluck('id')->toArray();
        }

        return $this->user->categories->pluck('id')->toArray();
    }

    public function getTickets()
    {
        if ($this->user->role !== 'user') {
            return Ticket::query();
        }

        $userDepartmentId = $this->user->department_id;

        return Ticket::where(function ($query) use ($userDepartmentId) {
            $query->whereIn('category_id', $this->getAllowedCategoryIds())
                ->orWhereIn('id', function ($subQuery) use ($userDepartmentId) {
                    $subQuery->select('ticket_id')
                        ->from('ticket_departments')
                        ->where('department_id', $userDepartmentId);
                });
        });
    }

    public function getTemplates()
    {
        if ($this->user->role !== 'user') {
            return Template::query();
        }

        return Template::whereIn('category_id', $this->getAllowedCategoryIds());
    }

    public function getCategories()
    {
        if ($this->user->role !== 'user') {
            return Category::all();
        }
        return $this->user->categories;
    }
    public function getUsers($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $departments = TicketDepartment::select('department_id')->where('ticket_id', $ticketId)->get();
        $userIds = UserCategory::select('user_id')->where('category_id', $ticket->category_id)->get();
        $users = User::whereIn('id', $userIds)->whereIn('department_id', $departments)->get();
        return $users;
    }
}
