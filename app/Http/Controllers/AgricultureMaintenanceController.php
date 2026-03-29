<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Models\Community;
use App\Models\Region;
use App\Models\ActionCategory;
use App\Models\AgricultureIssue;
use App\Models\AgricultureAction;
use Auth;
use DB;
use Route;
use DataTables;
use Excel;

class AgricultureMaintenanceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	

        if (Auth::guard('user')->user() != null) {
 
            $actionCategories = ActionCategory::where("is_archived", 0)->get();
            $agricultureActions = AgricultureAction::where("is_archived", 0)->get();

            return view('agriculture.issue.index', compact('actionCategories', 'agricultureActions'));
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
                
        return Excel::download(new EnergyActionExport($request), 'agriculture_actions.xlsx');
    }
}