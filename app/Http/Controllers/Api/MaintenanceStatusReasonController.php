<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\MaintenanceStatusReason;
use Illuminate\Support\Facades\Cache;
use Auth;
use DB;
use Route;

class MaintenanceStatusReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        $maintenanceStatusReasons = DB::table('maintenance_status_reasons')
            ->join('maintenance_statuses', 'maintenance_status_reasons.maintenance_status_id', 'maintenance_statuses.id')
            ->select(
                'maintenance_status_reasons.id as id', 
                'maintenance_status_reasons.arabic_name as reason', 
                'maintenance_statuses.arabic_name as status'
            )
            ->get();

        $data = collect([$maintenanceStatusReasons])->flatten();

        return response()->json([
            'icnidents' => $maintenanceStatusReasons ?: 'No data found'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}