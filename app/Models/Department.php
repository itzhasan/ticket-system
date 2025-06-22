<?php

namespace App\Models;

use App\Models\Template\Template;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;
    protected $guarded = ['id'];
     protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
     public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_departments');
    }
    public function ticketDepartment()
    {
        return $this->hasMany(TicketDepartment::class);
    }
    public function template(){
        return $this->hasMany(Template::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
