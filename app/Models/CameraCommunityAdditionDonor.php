<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraCommunityAdditionDonor extends Model
{
    use HasFactory;

    protected $fillable = [
        'camera_community_addition_id',
        'donor_id',
    ];

    public function cameraCommunityAddition()
    {
        return $this->belongsTo(CameraCommunityAddition::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
} 