<?php

namespace App\Models\Template;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldOption extends Model
{
    protected $guarded = ['id'];
    public function templateField()
    {
        return $this->belongsTo(TemplateFields::class, 'template_field_id');
    }
}
