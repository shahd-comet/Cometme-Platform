<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraCommunityReplacementDonor extends Model
{
    use HasFactory;

    protected $fillable = [
        'camera_community_replacement_id',
        'donor_id',
    ];

    public function cameraCommunityReplacement()
    {
        return $this->belongsTo(Replacement::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
} 