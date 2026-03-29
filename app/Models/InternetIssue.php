<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetIssue extends Model
{
    use HasFactory;

    public function InternetAction()
    {
        return $this->belongsTo(InternetAction::class, 'internet_action_id', 'id');
    }
}
