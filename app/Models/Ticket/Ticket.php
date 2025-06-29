<?php

namespace App\Models\Ticket;

use App\Models\Department;
use App\Models\Template\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'ticket_departments');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function ticketFieldsValues()
    {
        return $this->hasMany(TicketFieldsValue::class);
    }

    public function ticketDepartment()
    {
        return $this->belongsTo(TicketDepartment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
