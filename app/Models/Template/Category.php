<?php

namespace App\Models\Template;
use App\Models\Template\Template;
use App\Models\Ticket\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
     protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    public function template()
    {
        return $this->hasMany(Template::class);
    }
    public function ticket()
    {
        return $this->hasMany(Ticket::class);
    }
    public function userCategory()
    {
        return $this->hasMany(UserCategory::class);
    }
}
