<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureSystemComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'agriculture_system_id',
        'agriculture_component_id',
        'agriculture_component_model_id',
        'quantity',
        'unit_price',
        'total_price',
        'total_with_vet',
        'percentage',
        'is_archived'
    ];

    public function agricultureSystem()
    {
        return $this->belongsTo(AgricultureSystem::class, 'agriculture_system_id');
    }

    public function agricultureComponent()
    {
        return $this->belongsTo(AgricultureComponent::class, 'agriculture_component_id');
    }

    public function agricultureSystemComponentHolders()
    {
        return $this->hasMany(AgricultureSystemComponentHolder::class, 'agriculture_system_component_id');
    }
}
