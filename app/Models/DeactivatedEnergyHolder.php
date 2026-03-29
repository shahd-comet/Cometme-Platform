<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeactivatedEnergyHolder extends Model 
{
    use HasFactory;

    protected $fillable = ['all_energy_meter_id', 'meter_number', 'is_paid', 'visit_date', 'user_id'];

    public function AllEnergyMeter()
    {
        return $this->belongsTo(AllEnergyMeter::class, 'all_energy_meter_id', 'id');
    }
 
    public function User() 
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
