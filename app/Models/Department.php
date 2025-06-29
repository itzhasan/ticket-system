<?php

namespace App\Models;

use App\Models\Template\Template;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;
    protected $guarded = ['id'];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_departments');
    }
    public function ticketDepartment()
    {
        return $this->hasMany(TicketDepartment::class);
    }
    public function template()
    {
        return $this->hasMany(Template::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
