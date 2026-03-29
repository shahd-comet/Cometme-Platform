<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraCommunityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'camera_id',
        'camera_community_id',
        'number',
        'sd_card_number',
        'camera_base_number',
        'internet_cable_number'
    ];

    public function Camera()
    {
        
        return $this->belongsTo(Camera::class, 'camera_id', 'id');
    }
}
