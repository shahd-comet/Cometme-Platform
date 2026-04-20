<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_service_id', 
        'vendor_id',
        'visit_date',
        'collecting_date_from',
        'collecting_date_to',
        'total_amount_due',
        'amount_collected',
        'remaining_balance',
        'user_id',
    ]; 

    public function VendorService()
    {
        return $this->belongsTo(VendorService::class, 'vendor_service_id', 'id');
    }

    public function Vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}