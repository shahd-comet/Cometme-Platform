<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class CommunityVendor extends Model

{

    use HasFactory;

    protected $fillable = ['community_id', 'vendor_username_id', 'service_type_id', 'vendor_id'];
    

    public function Community()
    {

        return $this->belongsTo(Community::class, 'community_id', 'id');
    }

    public function VendorUserName()
    {

        return $this->belongsTo(VendorUserName::class, 'vendor_username_id', 'id');
    }

    public function ServiceType()
    {

        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id');
    }

    public function Vendor()
    {

        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

}

