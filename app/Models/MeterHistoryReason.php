<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterHistoryReason extends Model
{
    use HasFactory;

    protected $fillable = ['english_name', 'arabic_name', 'meter_history_status_id'];
}
 