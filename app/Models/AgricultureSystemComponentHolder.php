<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureSystemComponentHolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'agriculture_system_component_id',
        'agriculture_system_id',
        'agriculture_holder_id',
        'quantity',
        'unit_price',
        'is_archived'
    ];
    
    public function agricultureSystemComponent()
    {
        return $this->belongsTo(AgricultureSystemComponent::class, 'agriculture_system_component_id');
    }
    public function agricultureHolder() {
        return $this->belongsTo(AgricultureHolder::class, 'agriculture_holder_id');
    }
    public function agricultureSystem() {
        return $this->belongsTo(AgricultureSystem::class, 'agriculture_system_id');
    }
}
