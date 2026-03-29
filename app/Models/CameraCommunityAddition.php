<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CameraCommunityAdditionDonor;
use App\Models\Donor;
use App\Models\CameraCommunity;
use App\Models\Camera;
use App\Models\NvrCamera;
use App\Models\Compound;

class CameraCommunityAddition extends Model
{
    protected $fillable = [
        'camera_community_id',
        'date_of_addition',
        'number_of_cameras',
        'camera_id',
        'nvr_camera_id',
        'number_of_nvr',
        'notes',
        'compound_id',
        'sd_card_number',
    ];

    // ✅ أضف العلاقات التالية

    public function cameraCommunity()
    {
        return $this->belongsTo(CameraCommunity::class, 'camera_community_id');
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class, 'camera_id');
    }

    public function nvrCamera()
    {
        return $this->belongsTo(NvrCamera::class, 'nvr_camera_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function donors()
    {
        return $this->hasMany(\App\Models\CameraCommunityAdditionDonor::class, 'camera_community_addition_id');
    }
}
