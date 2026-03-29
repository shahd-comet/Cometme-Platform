<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterHistories extends Model
{
    use HasFactory;

    protected $table = 'meter_histories'; 

    protected $fillable = [
        'date',
        'meter_history_reason_id',
        'meter_history_status_id',
        'status_english_name',        // Status name from Excel
        'meter',                      // Meter number from Excel
        'community_id',
        'household_id',
        'publicstructure_id',
        'all_energy_meter_id',
        'old_meter_number',
        'comet_id',
        'new_meter_number',
        'new_household_id',
        'new_community_id',
        'new_public_structure_id',
        'main_energy_meter_id',
        'household_status',   
        'new_holder_status',   
        'notes',
        'new_meter_for_main_holder',  // New meter for main holder (if replaced)
        'shared_user_id'              // Shared User ID (if become a shared)
    ];

    public function reason()
    {
        return $this->belongsTo(MeterHistoryReason::class, 'meter_history_reason_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(MeterHistoryStatuses::class, 'meter_history_status_id', 'id');
    }
    
    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id', 'id');
    }

    public function household()
    {
        return $this->belongsTo(Household::class, 'household_id', 'id');
    }

    public function publicstructure()
    {
        return $this->belongsTo(PublicStructure::class, 'publicstructure_id', 'id');
    }

    public function newHousehold()
    {
        return $this->belongsTo(Household::class, 'new_household_id', 'id');
    }

    public function newCommunity()
    {
        return $this->belongsTo(Community::class, 'new_community_id', 'id');
    }

    public function newPublicStructure()
    {
        return $this->belongsTo(PublicStructure::class, 'new_public_structure_id', 'id');
    }

    public function allEnergyMeter()
    {
        return $this->belongsTo(AllEnergyMeter::class, 'all_energy_meter_id', 'id');
    }

    public function mainEnergyMeter()
    {
        return $this->belongsTo(AllEnergyMeter::class, 'main_energy_meter_id', 'id');
    }

    public function sharedUser()
    {
        // shared_user_id references the household_meters table (HouseholdMeter id)
        return $this->belongsTo(HouseholdMeter::class, 'shared_user_id', 'id');
    }

    /**
     * Friendly display name for the shared user.
     * Tries multiple sources: HouseholdMeter.household_name, HouseholdMeter.user_name,
     * or if HouseholdMeter has household_id, load Household->english_name.
     */
    public function getSharedUserNameAttribute()
    {
        if ($this->sharedUser) {
            // Prefer stored household_name or user_name on HouseholdMeter
            if (!empty($this->sharedUser->household_name)) {
                return $this->sharedUser->household_name;
            }
            if (!empty($this->sharedUser->user_name)) {
                return $this->sharedUser->user_name;
            }

            // Fallback: if HouseholdMeter points to a household_id, try to fetch Household
            if (!empty($this->sharedUser->household_id)) {
                $hh = Household::find($this->sharedUser->household_id);
                if ($hh) return $hh->english_name ?? $hh->arabic_name ?? null;
            }
        }
        return null;
    }
    /**
     * Get the main holder name (household or public structure)
     */
    public function getMainHolderNameAttribute()
    {
        if ($this->household) {
            return $this->household->english_name ?? $this->household->arabic_name;
        } elseif ($this->publicstructure) {
            return $this->publicstructure->english_name ?? $this->publicstructure->arabic_name;
        }
        return null;
    }

    /**
     * Get the new holder name (household or public structure)
     */
    public function getNewHolderNameAttribute()
    {
        if ($this->newHousehold) {
            return $this->newHousehold->english_name ?? $this->newHousehold->arabic_name;
        } elseif ($this->newPublicStructure) {
            return $this->newPublicStructure->english_name ?? $this->newPublicStructure->arabic_name;
        }
        return null;
    }

    /**
     * Check if main holder is a public structure
     */
    public function getIsPublicStructureAttribute()
    {
        return !empty($this->publicstructure_id);
    }

    /**
     * Check if new holder is a public structure
     */
    public function getIsNewHolderPublicStructureAttribute()
    {
        return !empty($this->new_public_structure_id);
    }
}
