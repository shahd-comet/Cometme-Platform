<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraComponentAccessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_name',
        'component_type',
        'description',
    ];

    protected $table = 'camera_components';
} 