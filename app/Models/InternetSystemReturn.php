<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetSystemReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'is_archived',
        'returned_by',
        'return_date',
        'reason',
        'status',
        'internet_system_id',
        'internet_system_community_id',
        'notes',


        ];

        public function internetSystem()
        {
            return $this->belongsTo(InternetSystem::class);
        }
        public function internetSystemCommunity()
        {
            return $this->belongsTo(InternetSystemCommunity::class);
        }
        public function items()
        {
            return $this->hasMany(InternetSystemReturnItem::class);
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'returned_by');
        }

}
