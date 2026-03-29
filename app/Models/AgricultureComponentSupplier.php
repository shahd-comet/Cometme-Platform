<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureComponentSupplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'agriculture_component_id',
        'agriculture_supplier_id',
        'is_archived'
    ];

    public function agricultureComponent()
    {
        return $this->belongsTo(AgricultureComponent::class, 'agriculture_component_id');
    }
    public function agricultureSupplier()
    {
        return $this->belongsTo(AgricultureSupplier::class, 'agriculture_supplier_id');
    }
    
}
