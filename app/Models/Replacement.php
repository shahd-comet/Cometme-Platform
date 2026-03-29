<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Replacement extends Model
{
    protected $table = 'camera_community_replacements';

    protected $fillable = [
        'camera_community_id',
        'date_of_replacement',
        'damaged_camera_count',
        'new_camera_count',
        'camera_id',
        'nvr_camera_id',
        'number_of_nvr',
        'notes',
        'camera_replacement_incident_id',
        'compound_id',
        'damaged_sd_card_count',
        'new_sd_card_count',
    ];

    public function cameraCommunity()
    {
        return $this->belongsTo(CameraCommunity::class);
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function nvrCamera()
    {
        return $this->belongsTo(NvrCamera::class);
    }

    public function cameraReplacementIncident()
    {
        return $this->belongsTo(CameraReplacementIncident::class);
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function donors()
    {
        return $this->hasMany(CameraCommunityReplacementDonor::class, 'camera_community_replacement_id');
    }
}
