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
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityDonor;
use App\Models\Donor;
use App\Models\EnergyDonor;
use App\Models\EnergySystem;
use App\Models\EnergySystemType;
use App\Models\EnergyUser;
use App\Models\EnergyHolder;
use App\Models\EnergyPublicStructure;
use App\Models\EnergyPublicStructureDonor;
use App\Models\Household;
use App\Models\HouseholdMeter;
use App\Models\MeterCase;
use App\Models\ServiceType;
use App\Models\PublicStructure;
use App\Models\PublicStructureCategory;
use App\Models\Region;
use App\Models\HouseholdStatus;
use App\Models\InstallationType;
use App\Models\VendorUserName;
use App\Exports\HouseholdMeters;
use App\Helpers\SequenceHelper;
use Carbon\Carbon;
use Image;
use DataTables;
use Excel;

class HouseholdMeterController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $viewButton = "<a type='button' class='viewHouseholdMeterUser' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#viewEnergySharedUserModal' ><i class='fa-solid fa-eye text-info'></i></a>";
        $deleteButton = "<a type='button' class='deleteAllHouseholdMeterUser' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        
        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ||
            Auth::guard('user')->user()->user_type_id == 3 ||
            Auth::guard('user')->user()->user_type_id == 4 ||
            Auth::guard('user')->user()->user_type_id == 12) 
        {
                
            return $viewButton." ". $deleteButton;
        } else return $viewButton;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::guard('user')->user() != null) {

            $communityFilter = $request->input('community_filter');
            $typeFilter = $request->input('type_filter');
            $dateFilter = $request->input('date_filter');
            $yearFilter = $request->input('year_filter');
            $meterFilter = $request->input('meter_filter');
            $regionFilter = $request->input('region_filter');
            $energyTypeFilter = $request->input('system_type_filter'); 
            $cycleFilter = $request->input('cycle_filter');

            if ($request->ajax()) {

                $data = DB::table('household_meters')
                    ->join('all_energy_meters', 'household_meters.energy_user_id', 'all_energy_meters.id')
                    ->leftJoin('households as main_households', 'all_energy_meters.household_id', 'main_households.id')
                    ->leftJoin('public_structures as main_publics', 'all_energy_meters.public_structure_id', 'main_publics.id')
                    ->leftJoin('households', 'household_meters.household_id', 'households.id')
                    ->leftJoin('public_structures', 'household_meters.public_structure_id', 
                        'public_structures.id')
                    ->join('communities', 'all_energy_meters.community_id', 'communities.id')
                    ->where('household_meters.is_archived', 0);
                
                $data->when($regionFilter, fn($q) => $q->where('communities.region_id', $regionFilter))
                    ->when($communityFilter, fn($q) => $q->where('communities.id', $communityFilter))
                    ->when($typeFilter, fn($q) => $q->where('all_energy_meters.installation_type_id', $typeFilter))
                    ->when($energyTypeFilter, fn($q) => $q->where('all_energy_meters.energy_system_type_id', $energyTypeFilter))
                    ->when($meterFilter, fn($q) => $q->where('all_energy_meters.meter_case_id', $meterFilter))
                    ->when($cycleFilter, fn($q) => $q->where('all_energy_meters.energy_system_cycle_id', $cycleFilter))
                    ->when($yearFilter, fn($q) => $q->where('all_energy_meters.installation_date', $yearFilter))
                    ->when($dateFilter, fn($q) => $q->where('all_energy_meters.installation_date', '>=', $dateFilter));

                $search = $request->input('search.value'); 

                if (!empty($search)) {
                    $data->where(function ($w) use ($search) {
                        $w->where('communities.english_name', 'LIKE', "%$search%")
                        ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('households.english_name', 'LIKE', "%$search%")
                        ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('all_energy_meters.meter_number', 'LIKE', "%$search%")
                        ->orWhere('public_structures.english_name', 'LIKE', "%$search%")
                        ->orWhere('public_structures.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('main_households.english_name', 'LIKE', "%$search%")
                        ->orWhere('main_households.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('main_publics.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('main_publics.english_name', 'LIKE', "%$search%");
                    });
                }

                $totalRecords = DB::table('household_meters')
                    ->where('is_archived', 0)
                    ->count();

                $filteredRecords = (clone $data)->count();

                $data = $data->select(
                    'communities.english_name as community_name',
                    'household_meters.id as id', 'household_meters.created_at',
                    'household_meters.updated_at',
                    DB::raw('IFNULL(households.english_name, public_structures.english_name) as holder'),
                    DB::raw('IFNULL(main_households.english_name, main_publics.english_name) as user_name'),
                    DB::raw('IFNULL(main_households.arabic_name, main_publics.arabic_name) as user_name_arabic'),
                    DB::raw("'action' AS action")
                    )
                    ->distinct()
                    ->latest()
                    ->skip($request->start)->take($request->length)
                    ->get();


                foreach ($data as $row) {

                    $row->action = $this->generateActionButtons($row); // Add the action buttons
                }

                return response()->json([
                    "draw" => $request->draw,  // DataTables draw count
                    "recordsTotal" => $totalRecords,
                    "recordsFiltered" => $filteredRecords,
                    "data" => $data
                ]); 
            }
            
        } else {

            return view('errors.not-found');
        }
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $householdMeter = HouseholdMeter::findOrFail($id);
        $mainUser = AllEnergyMeter::findOrFail($householdMeter->energy_user_id);
        $user = Household::where('id', $mainUser->household_id)->first();
        $sharedUser = Household::where('id', $householdMeter->household_id)->first();
        $sharedPublic = PublicStructure::where('id', $householdMeter->public_structure_id)->first();

        $community = Community::where('id', $user->community_id)->first();
        $meter = MeterCase::where('id', $mainUser->meter_case_id)->first();
        $systemType = EnergySystemType::where('id', $mainUser->energy_system_type_id)->first();
        $system = EnergySystem::where('id', $mainUser->energy_system_id)->first();
        $vendor = VendorUserName::where('id', $mainUser->vendor_username_id)->first();
        $installationType = InstallationType::where('id', $mainUser->installation_type_id)->first();

        $response['user'] = $user;
        $response['mainUser'] = $mainUser;
        $response['sharedUser'] = $sharedUser;
        $response['sharedPublic'] = $sharedPublic;
        $response['householdMeters'] = $householdMeter;
        $response['community'] = $community;
        $response['meter'] = $meter;
        $response['type'] = $systemType;
        $response['system'] = $system;
        $response['vendor'] = $vendor;
        $response['installationType'] = $installationType;

        return response()->json($response);
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getUsers($id)
    {
        $community = Community::findOrFail($id);

        if (!$id) {

            $html = '<option value="">Choose One...</option>';
        } else {

            $html = '<option selected>Choose One...</option>';
            $users = DB::table('all_energy_meters')
                ->where('all_energy_meters.is_archived', 0)
                ->where('all_energy_meters.community_id', $community->id)
                ->join('households', 'all_energy_meters.household_id', 'households.id')
                // ->leftJoin('public_structures', 'all_energy_meters.public_structure_id', 
                //     'public_structures.id')
                ->select('all_energy_meters.id', 
                    'households.english_name')
                    //DB::raw('IFNULL(households.english_name, public_structures.english_name) as english_name'))
                // ->orderByRaw('CASE 
                //     WHEN households.english_name IS NOT NULL THEN 0 
                //     WHEN public_structures.english_name IS NOT NULL THEN 1 
                //     ELSE 2 
                //     END')
                ->orderBy('english_name', 'ASC')
                ->get();

            foreach ($users as $user) {
                $html .= '<option value="'.$user->id.'">'.$user->english_name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
    }


    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getHouseholds($id)
    {
        $energyUser = AllEnergyMeter::findOrFail($id);

        if (!$id) {

            $html = '<option value="">Choose One...</option>';
        } else {

            $html = '<option disabled selected>Choose One...</option>';
            $households = DB::table('households')
                ->where('households.community_id', $energyUser->community_id)
                ->where('households.id', '!=', $energyUser->household_id)
                ->leftJoin('all_energy_meters', 'households.id', 'all_energy_meters.household_id')
                ->whereNull('all_energy_meters.household_id')
                ->where('households.is_archived', 0)
                ->select('households.id', 'households.english_name')
                ->orderBy('households.english_name', 'ASC')
                ->get();

            foreach ($households as $household) {
                $html .= '<option value="'.$household->id.'">'.$household->english_name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
    }

     /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getPublicStructures($id)
    {
        $energyUser = AllEnergyMeter::findOrFail($id);
       
        if (!$id) {

            $html = '<option value="">Choose One...</option>';
        } else {

            $html = '<option disabled selected>Choose One...</option>';

            $publics = DB::table('public_structures')
                ->where('public_structures.community_id', $energyUser->community_id)
                ->where('public_structures.id', '!=', $energyUser->public_structure_id)
                ->leftJoin('all_energy_meters', 'public_structures.id', 
                    'all_energy_meters.public_structure_id')
                ->whereNull('all_energy_meters.public_structure_id')
                ->where('public_structures.is_archived', 0)
                ->select('public_structures.id', 'public_structures.english_name')
                ->orderBy('public_structures.english_name', 'ASC')
                ->get();

            foreach ($publics as $public) {
                $html .= '<option value="'.$public->id.'">'.$public->english_name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $energyUser = AllEnergyMeter::where('id', $request->energy_user_id)->first();
        $mainHousehold = Household::where('id', $energyUser->household_id)->first();
        
        if($request->household_id) {

            for($i=0; $i < count($request->household_id); $i++) {

                if($energyUser) {

                    $mainHousehold = Household::where("is_archived", 0)->where("id", $energyUser->household_id)->first();
                    $household = Household::where('id', $request->household_id[$i])->first();

                    if($mainHousehold) {

                        // Served, no meter
                        if($energyUser->meter_number == 0 && $mainHousehold->household_status_id == 14) $household->household_status_id = 14;
                        // AC completed
                        if($energyUser->meter_number == 0 && $mainHousehold->household_status_id == 3) $household->household_status_id = 3;
                    }
                    
                    else $household->household_status_id = 4;
                    
                    $household->save();

                    $newAllEnergyMeter = new AllEnergyMeter();
                    $newAllEnergyMeter->household_id = $request->household_id[$i];
                    $newAllEnergyMeter->is_main = "No";
                    $newAllEnergyMeter->community_id = $energyUser->community_id;
                    $newAllEnergyMeter->installation_type_id = 4;
                    $newAllEnergyMeter->energy_system_type_id = $energyUser->energy_system_type_id;
                    $newAllEnergyMeter->energy_system_id  = $energyUser->energy_system_id;

                    $lastIncrementalNumber = AllEnergyMeter::whereNotNull('fake_meter_number')
                        ->selectRaw('MAX(CAST(SUBSTRING_INDEX(fake_meter_number, \'s\', -1) AS UNSIGNED)) AS incremental_number')
                        ->value('incremental_number');

                    $lastIncrementalNumber = $lastIncrementalNumber + 1; 
                    $newFakeMeterNumber = SequenceHelper::generateSequence($energyUser->meter_number, $lastIncrementalNumber);
                    $newAllEnergyMeter->fake_meter_number = $newFakeMeterNumber;

                    $newAllEnergyMeter->save();
                }

                $householdMeter = new HouseholdMeter();
                $householdMeter->user_name = $mainHousehold->english_name;
                $householdMeter->user_name_arabic = $mainHousehold->arabic_name;
                $householdMeter->household_id = $request->household_id[$i];
                $householdMeter->energy_user_id = $request->energy_user_id;
                $householdMeter->save();
            }
        }

        if($request->public_id) {

            for($i=0; $i < count($request->public_id); $i++) {

                if($energyUser) {

                    $public = PublicStructure::where('id', $request->public_id[$i])->first();
          
                    $newAllEnergyMeter = new AllEnergyMeter();
                    $newAllEnergyMeter->public_structure_id = $request->public_id[$i];
                    $newAllEnergyMeter->is_main = "No";
                    $newAllEnergyMeter->community_id = $energyUser->community_id;
                    $newAllEnergyMeter->installation_type_id = 4;
                    $newAllEnergyMeter->energy_system_type_id = $energyUser->energy_system_type_id;
                    $newAllEnergyMeter->energy_system_id = $energyUser->energy_system_id ;

                    $lastIncrementalNumber = AllEnergyMeter::whereNotNull('fake_meter_number')
                        ->selectRaw('MAX(CAST(SUBSTRING_INDEX(fake_meter_number, \'s\', -1) AS UNSIGNED)) AS incremental_number')
                        ->value('incremental_number');

                    $lastIncrementalNumber = $lastIncrementalNumber + 1; 
                    $newFakeMeterNumber = SequenceHelper::generateSequence($energyUser->meter_number, $lastIncrementalNumber);
                    $newAllEnergyMeter->fake_meter_number = $newFakeMeterNumber;
                    
                    $newAllEnergyMeter->save();
                }

                $householdMeter = new HouseholdMeter();
                $householdMeter->user_name = $mainHousehold->english_name;
                $householdMeter->user_name_arabic = $mainHousehold->arabic_name;
                $householdMeter->public_structure_id = $request->public_id[$i];
                $householdMeter->energy_user_id = $request->energy_user_id;
                $householdMeter->save();
            }
        }
        
        return redirect()->back()->with('message', 'New Shared Holders Added Successfully!');
    }

     /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteHouseholdMeter(Request $request)
    {
        $id = $request->id;

        $householdMeter = HouseholdMeter::find($id);

        if($householdMeter) {

            $allEnergyMeter = AllEnergyMeter::where("household_id", $householdMeter->household_id)->first();
            $allEnergyMeter->delete();

            $householdMeter->is_archived = 1;
            $householdMeter->save();

            $response['success'] = 1;
            $response['msg'] = 'Household Meter Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request) 
    {

        return Excel::download(new HouseholdMeters($request), 'shared_users.xlsx');
    }

    /**
     * Display households with status "Served, no meter" (DC / Active No Meter)
     */
    public function dcIndex(Request $request)
    {
        if (Auth::guard('user')->user() != null) {

            if ($request->ajax()) {

                $statusText = "Served, no meter";
                $statusObj = HouseholdStatus::where('status', 'like', '%' . $statusText . '%')->first();

                $data = DB::table('households')
                    ->where('households.is_archived', 0)
                    ->join('communities', 'households.community_id', '=', 'communities.id')
                    ->join('regions', 'communities.region_id', '=', 'regions.id');

                if ($statusObj) {
                    $data->where('households.household_status_id', $statusObj->id);
                }

                // Apply frontend filters if provided
                $communityFilter = $request->input('community_filter');
                $regionFilter = $request->input('region_filter');
                $systemTypeFilter = $request->input('system_type_filter');

                if ($communityFilter) {
                    $data->where('communities.id', $communityFilter);
                }
                if ($regionFilter) {
                    $data->where('regions.id', $regionFilter);
                }
                if ($systemTypeFilter) {
                    $data->where('households.energy_system_type_id', $systemTypeFilter);
                }

                $data->select('households.english_name as english_name', 'households.arabic_name as arabic_name',
                        'households.id as id', 'households.created_at as created_at', 
                        'households.updated_at as updated_at',
                        'regions.english_name as region_name',
                        'communities.english_name as name',
                        'communities.arabic_name as aname')
                    ->latest(); 

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row) {

                        $detailsButton = "<a type='button' class='detailsHouseholdButton' data-bs-toggle='modal' data-bs-target='#householdDetails' data-id='".$row->id."'><i class='fa-solid fa-eye text-primary'></i></a>";
                        $updateButton = "<a type='button' class='updateHousehold' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
                        $deleteButton = "<a type='button' class='deleteHousehold' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
                        
                        if(Auth::guard('user')->user()->user_type_id != 7 || 
                            Auth::guard('user')->user()->user_type_id != 11 || 
                            Auth::guard('user')->user()->user_type_id != 8) 
                        {
                                
                            return $detailsButton." ". $updateButton." ".$deleteButton;
                        } else return $detailsButton; 
                    })
                   
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                                $instance->where(function($w) use($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('households.english_name', 'LIKE', "%$search%")
                                    ->orWhere('communities.english_name', 'LIKE', "%$search%")
                                    ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                                    ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                                    ->orWhere('regions.english_name', 'LIKE', "%$search%")
                                    ->orWhere('regions.arabic_name', 'LIKE', "%$search%");
                            });
                        }
                    })
                ->rawColumns(['action'])
                ->make(true);
            }

            return view('employee.household.served');
        } else {

            return view('errors.not-found');
        }
    }
}