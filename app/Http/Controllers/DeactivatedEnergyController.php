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
use App\Models\AllEnergyMeterDonor;
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityDonor;
use App\Models\CometMeter; 
use App\Models\CometMeterDonor;
use App\Models\Donor; 
use App\Models\DeactivatedEnergyHolder;
use App\Models\EnergySystem;
use App\Models\EnergySystemType;
use App\Models\EnergyUser;
use App\Models\EnergyHolder;
use App\Models\EnergyPublicStructure;
use App\Models\EnergyPublicStructureDonor;
use App\Models\InstallationType;
use App\Models\Household;
use App\Models\PublicStructure;
use App\Imports\ImportReactivatedHolder;
use Carbon\Carbon;
use Image;
use DataTables;
use Excel;

class DeactivatedEnergyController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $viewButton = "<a type='button' class='viewDeactivatedEnergyHolder' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#viewDeactivatedEnergyHolderModal' ><i class='fa-solid fa-eye text-info'></i></a>";
        $updateButton = "<a type='button' class='updateDeactivatedEnergyHolder' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteDeactivatedEnergyHolder' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        
        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ||
            Auth::guard('user')->user()->user_type_id == 3 || 
            Auth::guard('user')->user()->user_type_id == 4 ||
            Auth::guard('user')->user()->user_type_id == 12||
            Auth::guard('user')->user()->role_id == 21) 
        {
                
            return $viewButton." ". $updateButton." ".$deleteButton;
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

                $data = DB::table('deactivated_energy_holders') 
                    ->join('users', 'deactivated_energy_holders.user_id', 'users.id')
                    ->join('all_energy_meters', 'deactivated_energy_holders.all_energy_meter_id', 'all_energy_meters.id')
                    ->leftJoin('households', 'all_energy_meters.household_id', 'households.id')
                    ->leftJoin('public_structures', 'all_energy_meters.public_structure_id', 'public_structures.id')
                    ->join('communities', 'all_energy_meters.community_id', 'communities.id')
                    ->join('energy_systems', 'all_energy_meters.energy_system_id', 'energy_systems.id')
                    ->join('energy_system_types', 'all_energy_meters.energy_system_type_id', 'energy_system_types.id')
                    ->join('meter_cases', 'all_energy_meters.meter_case_id', 'meter_cases.id')
                    ->where('deactivated_energy_holders.is_archived', 0);
                
                $data->when($regionFilter, fn($q) => $q->where('communities.region_id', $regionFilter))
                    ->when($communityFilter, fn($q) => $q->where('communities.id', $communityFilter))
                    ->when($typeFilter, fn($q) => $q->where('all_energy_meters.installation_type_id', $typeFilter))
                    ->when($energyTypeFilter, fn($q) => $q->where('all_energy_meters.energy_system_type_id', $energyTypeFilter))
                    ->when($meterFilter, fn($q) => $q->where('all_energy_meters.meter_case_id', $meterFilter))
                    ->when($cycleFilter, fn($q) => $q->where('all_energy_meters.energy_system_cycle_id', $cycleFilter))
                    ->when($yearFilter, fn($q) => $q->where('all_energy_meters.installation_date', $yearFilter))
                    ->when($dateFilter, fn($q) => $q->where('deactivated_energy_holders.visit_date', '>=', $dateFilter));

                $search = $request->input('search.value'); 

                if (!empty($search)) {
                    $data->where(function ($w) use ($search) {
                        $w->where('communities.english_name', 'LIKE', "%$search%")
                            ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('energy_systems.name', 'LIKE', "%$search%")
                            ->orWhere('energy_system_types.name', 'LIKE', "%$search%")
                            ->orWhere('public_structures.english_name', 'LIKE', "%$search%")
                            ->orWhere('public_structures.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('households.english_name', 'LIKE', "%$search%")
                            ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('deactivated_energy_holders.meter_number', 'LIKE', "%$search%");
                    });
                }

                $totalRecords = DB::table('deactivated_energy_holders')
                    ->where('is_archived', 0)
                    ->count();

                $filteredRecords = (clone $data)->count();

                $data = $data->select(
                    'deactivated_energy_holders.meter_number', 
                    'deactivated_energy_holders.id as id', 
                    'deactivated_energy_holders.is_paid', 
                    'deactivated_energy_holders.visit_date', 
                    'deactivated_energy_holders.is_return', 
                    'deactivated_energy_holders.deactivated_after_war', 
                    'communities.english_name as community_name',
                    DB::raw('IFNULL(households.english_name, public_structures.english_name) 
                        as holder'),
                    'users.name as user_name',
                    DB::raw("'action' AS action")
                    )
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $energyHolder = null;
        $reactivatedHolder = DeactivatedEnergyHolder::findOrFail($id);
        $energyMeter = AllEnergyMeter::findOrFail($reactivatedHolder->all_energy_meter_id);

        
        if($energyMeter->household_id) $energyHolder = Household::findOrFail($energyMeter->household_id);
        elseif($energyMeter->public_structure_id) $energyHolder = PublicStructure::findOrFail($energyMeter->public_structure_id);

        $user = User::findOrFail($reactivatedHolder->user_id);
        $community = Community::findOrFail($energyMeter->community_id);
        $systemType = EnergySystemType::findOrFail($energyMeter->energy_system_type_id);
        $system = EnergySystem::findOrFail($energyMeter->energy_system_id);

        $response['reactivatedHolder'] = $reactivatedHolder;
        $response['energyHolder'] = $energyHolder;
        $response['community'] = $community;
        $response['energyMeter'] = $energyMeter;
        $response['systemType'] = $systemType;
        $response['system'] = $system;
        $response['user'] = $user;

        return response()->json($response);
    }

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function editPage($id)
    {
        $reactivatedHolder = DeactivatedEnergyHolder::findOrFail($id);

        return response()->json($reactivatedHolder);
    } 

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        $reactivatedHolder = DeactivatedEnergyHolder::findOrFail($id);
        $users = User::where('is_archived', 0)->get();

        return view('users.energy.reactivated.edit', compact('reactivatedHolder', 'users'));
    }
    
    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $reactivatedHolder = DeactivatedEnergyHolder::find($id);

        $reactivatedHolder->visit_date = $request->visit_date;
        $reactivatedHolder->is_paid = $request->is_paid;
        $reactivatedHolder->paid_amount = $request->paid_amount;
        $reactivatedHolder->user_id = $request->user_id;
        $reactivatedHolder->deactivated_after_war = $request->deactivated_after_war;
        $reactivatedHolder->is_return = $request->is_return;
        $reactivatedHolder->reactivation_date = $request->reactivation_date;
        $reactivatedHolder->system_status = $request->system_status;
        $reactivatedHolder->notes = $request->notes;
        $reactivatedHolder->save(); 

        return redirect('/all-meter')->with('message', 'Reactivated Holder Updated Successfully!');
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteReactivatedHolder(Request $request)
    {
        $id = $request->id;

        $reactivatedHolder = DeactivatedEnergyHolder::find($id);

        if($reactivatedHolder) {

            $reactivatedHolder->is_archived = 1;
            $reactivatedHolder->save();
            
            $response['success'] = 1;
            $response['msg'] = 'Reactivated Holder Delete successfully'; 
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
    public function import(Request $request)
    {
        Excel::import(new ImportReactivatedHolder, $request->file('excel_file')); 
            
        return back()->with('success', 'Reactivated Data Imported successfully.');
    }
}
