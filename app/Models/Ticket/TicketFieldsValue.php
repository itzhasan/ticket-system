<?php

namespace App\Models\Ticket;

use App\Models\Template\TemplateFields;
use Illuminate\Database\Eloquent\Model;

class TicketFieldsValue extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
     public function templateField()
    {
        return $this->belongsTo(TemplateFields::class);
    }
}
