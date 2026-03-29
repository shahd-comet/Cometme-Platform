<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;
    protected $table='towns';
    protected $fillable = ['comet_id','is_archived','english_name','arabic_name','region_name','region_id'];
    public $timestamps = true;

    /**
     * Boot the model and set up model event listeners.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($town) {
            if (empty($town->comet_id)) {
                $town->comet_id = static::generateCometId();
            }
        });
    }

    /**
     * Generate a unique comet_id starting with TW1000
     */
    public static function generateCometId()
    {
        $prefix = 'TW1000';
        $lastTown = static::where('comet_id', 'like', $prefix . '%')
                           ->orderBy('comet_id', 'desc')
                           ->first();
        
        if ($lastTown && $lastTown->comet_id) {
            // Extract the number part and increment it
            $lastNumber = (int) substr($lastTown->comet_id, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            // Start with 1 if no previous records
            $nextNumber = 1;
        }
        
        return $prefix . $nextNumber;
    }

    public function region()
{
    return $this->belongsTo(Region::class, 'region_id');
}

}

