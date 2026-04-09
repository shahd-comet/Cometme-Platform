<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetSystemReturnItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'internet_system_return_id',
        'component_id',
        'component_type',
        'quantity',
        'notes',
    ];
    
    public function return()
    {
        return $this->belongsTo(InternetSystemReturn::class, 'internet_system_return_id');
    }

    public function component()
    {
        return $this->morphTo();
    }


}
