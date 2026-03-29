<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TownHolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'comet_id',
        'arabic_name',
        'phone_number',
        'has_internet',
        'town_id',
        'community_id',
        'is_activist'
    ]; 

    public function Town()  
    {
        return $this->belongsTo(Town::class, 'town_id', 'id');
    }

    public function Community()  
    {
        return $this->belongsTo(Community::class, 'community_id', 'id');
    }
}
