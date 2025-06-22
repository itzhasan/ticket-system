<?php

namespace App\Models\Template;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;

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

    // Count total fields
    public function getFieldsCountAttribute()
    {
        return $this->fields()->count();
    }

    // Count required fields
    public function getRequiredFieldsCountAttribute()
    {
        return $this->fields()->where('required', true)->count();
    }
      public function hasFields()
    {
        return $this->fields()->exists();
    }

    // Get next order number for new field
    public function getNextFieldOrder()
    {
        return $this->fields()->max('order') + 1;
    }
}
