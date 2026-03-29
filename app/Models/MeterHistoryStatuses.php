<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterHistoryStatuses extends Model
{
    use HasFactory;
    protected $fillable = ['english_name', 'arabic_name'];
}
 