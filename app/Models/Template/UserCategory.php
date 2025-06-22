<?php

namespace App\Models\Template;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
