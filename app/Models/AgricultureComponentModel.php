<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureComponentModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'is_archived',
        'agriculture_component_id',
        'model',
        'brand',
        'unit',
        'specification',
    ];
    
    public function agricultureComponent()
    {
        return $this->belongsTo(AgricultureComponent::class, 'agriculture_component_id');
    }
}

