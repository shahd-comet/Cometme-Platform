<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetSystemCable extends Model
{
    use HasFactory;

    public function returnItems()
    {
        return $this->morphMany(InternetSystemReturnItem::class, 'component');
    }
}
  