<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureIssue extends Model
{
    use HasFactory;

    
    public function AgricultureAction()
    {
        return $this->belongsTo(AgricultureAction::class, 'agriculture_action_id', 'id');
    }
}
