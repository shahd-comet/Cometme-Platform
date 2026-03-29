<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetUser extends Model
{
    use HasFactory;  

    protected $fillable = [
        'internet_status_id',
        'household_id',
        'public_structure_id',
        'town_holder_id',
        'community_id',
        'start_date',
        'active',
        'last_purchase_date',
        'expired_gt_than_30d',
        'expired_gt_than_60d',
        'is_expire',
        'paid',
        'is_hotspot',
        'is_ppp',
        'from_api',
        'number_of_people',
    ];

    public function Community()
    {
        return $this->belongsTo(Community::class, 'community_id', 'id');
    }

    public function TownHolder()
    {
        return $this->belongsTo(TownHolder::class, 'town_holder_id', 'id');
    }

    public function Household()
    {
        return $this->belongsTo(Household::class, 'household_id', 'id');
    }

    public function PublicStructure()
    {
        
        return $this->belongsTo(PublicStructure::class, 'public_structure_id', 'id');
    }
}
