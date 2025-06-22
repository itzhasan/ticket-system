<?php

namespace App\Models\Ticket;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;

class TicketDepartment extends Model
{
    protected $guarded = ['id'];
     protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }
}
