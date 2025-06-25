<?php

namespace App\Services;

use App\Models\Template\Category;
use App\Models\Template\Template;
use App\Models\Ticket\Ticket;
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

        return Ticket::whereIn('category_id', $this->getAllowedCategoryIds());
    }

    public function getTemplates()
    {
        if ($this->user->role !== 'user') {
            return Template::query();
        }

        return Template::whereIn('category_id', $this->getAllowedCategoryIds());
    }

    public function hasAccessToTicket(Ticket $ticket): bool
    {
        if ($this->user->role !== 'user') {
            return true;
        }

        return in_array($ticket->category_id, $this->getAllowedCategoryIds());
    }

    public function hasAccessToTemplate(Template $template): bool
    {
        if ($this->user->role !== 'user') {
            return true;
        }

        return in_array($template->category_id, $this->getAllowedCategoryIds());
    }
    public function getCategories()
    {
        if ($this->user->role !== 'user') {
            return Category::all();
        }
        return $this->user->categories;
    }
}
