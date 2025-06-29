<?php

namespace App\Models\Template;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{

    protected $guarded = ['id'];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function templates()
    {
        return $this->hasMany(Template::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function fields()
    {
        return $this->hasMany(TemplateFields::class)->orderBy('order');
    }
    public function requiredFields()
    {
        return $this->hasMany(TemplateFields::class)->where('required', true)->orderBy('order');
    }
    public function templateFields()
    {
        return $this->hasMany(TemplateFields::class)->orderBy('order');
    }

    // Get optional fields only
    public function optionalFields()
    {
        return $this->hasMany(TemplateFields::class)->where('required', false)->orderBy('order');
    }

    public function getFieldsCountAttribute()
    {
        return $this->fields()->count();
    }

    public function getRequiredFieldsCountAttribute()
    {
        return $this->fields()->where('required', true)->count();
    }
    public function hasFields()
    {
        return $this->fields()->exists();
    }

    public function getNextFieldOrder()
    {
        return $this->fields()->max('order') + 1;
    }
}
