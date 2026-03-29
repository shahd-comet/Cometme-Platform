<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB; 
use Route;
use App\Models\AllEnergyMeter;
use App\Models\AllWaterHolder;
use App\Models\AllEnergyMeterDonor;
use App\Models\User; 
use App\Models\Community; 
use App\Models\Town; 
use Carbon\Carbon;
use App\Exports\TownHolderExport;
use App\Exports\ActivistHolderExport;
use App\Exports\InternalHolderExport;
use Image;
use Excel; 
use DataTables;

class OtherHolderController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::guard('user')->user() != null) {
 
            $towns = Town::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();

            $communities = Community::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();

            return view('holders.index', compact('towns', 'communities'));
        } else {

            return view('errors.not-found');
        }
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request) 
    {
        if($request->file_type == "town_holders") {
                
            return Excel::download(new TownHolderExport($request), 'Town Holders.xlsx');
        }
        if($request->file_type == "activist_holders") {
                
            return Excel::download(new ActivistHolderExport($request), 'Activist Holders.xlsx');
        }
        if($request->file_type == "internal_holders") {
                
            return Excel::download(new InternalHolderExport($request), 'Internal Holders.xlsx');
        }
    }
} 