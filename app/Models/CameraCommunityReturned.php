<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CameraCommunityReturned extends Model
{
    protected $table = 'camera_community_returned';

    protected $fillable = [
        'camera_community_id',
        'compound_id',
        'date',
        'camera_id',
        'number_of_cameras',
        'sd_card_number',
        'nvr_camera_id',
        'number_of_nvr',
        'notes',
        'is_archived',
        'repository_id',
        'status',
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
        return $this->belongsTo(NvrCamera::class, 'nvr_camera_id');
    }

    public function compound()
    {
        return $this->belongsTo(Compound::class, 'compound_id');
    }

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repository_id');
    }
}
