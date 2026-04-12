<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorService extends Model
{
    use HasFactory;

    protected $fillable = ['service_type_id', 'vendor_id'];

    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id');
    }

    public function Vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }
}
