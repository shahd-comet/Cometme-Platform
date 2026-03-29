<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'english_name',
        'arabic_name',
        'notes',
        'is_custom',
        'is_archived',
        'agriculture_component_category_id'
    ];

    public function category()
    {
        return $this->belongsTo(AgricultureComponentCategory::class, 'agriculture_component_category_id');
    }

}
