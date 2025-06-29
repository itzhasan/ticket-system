<?php

namespace App\Models\Template;

use App\Models\Ticket\TicketFieldsValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateFields extends Model
{

    protected $guarded = ['id'];
    protected $casts = [
        'required' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    public function fieldOptions()
    {
        return $this->hasMany(FieldOption::class, 'template_field_id');
    }
    public function ticketFields()
    {
        return $this->hasMany(TicketFieldsValue::class);
    }
    // Get options as array
    public function getOptionsArrayAttribute()
    {
        if (empty($this->options)) {
            return [];
        }

        return array_filter(array_map('trim', explode("\n", $this->options)));
    }

    // Scope for ordering fields
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Scope for required fields
    public function scopeRequired($query)
    {
        return $query->where('required', true);
    }

    // Check if field has options
    public function hasOptions()
    {
        return in_array($this->type, ['select', 'radio', 'checkbox']) && !empty($this->options);
    }

    // Get field type label
    public function getTypeLabel()
    {
        $types = [
            'text' => 'Text Input',
            'textarea' => 'Text Area',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buttons',
            'date' => 'Date Picker',
            'file' => 'File Upload'
        ];

        return $types[$this->type] ?? 'Unknown';
    }
}
