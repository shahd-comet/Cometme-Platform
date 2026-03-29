<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\AllMaintenanceTicket;
use App\Models\AllMaintenanceTicketAction;
use App\Models\WorkshopCommunity;
use App\Models\AllEnergyMeter;
use App\Models\User;
use App\Models\Community;
use App\Models\EnergySystem;
use App\Models\Household;
use App\Models\EnergyIssue;
use App\Models\InternetIssue;
use App\Models\WaterIssue;
use App\Models\MaintenanceStatus;
use App\Models\MaintenanceStatusReason;
use App\Models\MaintenanceType;
use App\Models\PublicStructure;
use App\Models\WaterSystem;
use App\Models\ServiceType;
use App\Models\EnergyTurbineCommunity;
use App\Models\EnergyGeneratorCommunity;
use App\Exports\Maintenance\AllMaintenanceExport;
use App\Imports\ImportEnergyMaintenance;
use Auth;
use DB;
use Route;
use DataTables;
use Excel;

class AllMaintenanceController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $viewButton = "<a type='button' class='viewAllMaintenance' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#viewAllMaintenanceModal' ><i class='fa-solid fa-eye text-info'></i></a>";
        
        return $viewButton;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        $agentFilter = $request->input('agent_filter');
        $communityFilter = $request->input('community_filter');
        $typeFilter = $request->input('type_filter');
        $statusFilter = $request->input('status_filter');
        $serviceFilter = $request->input('service_filter');
        $reasonFilter = $request->input('reason_filter');

        if (Auth::guard('user')->user() != null) {

            if ($request->ajax()) {

                $data = DB::table('all_maintenance_tickets')
                    ->join('service_types', 'all_maintenance_tickets.service_type_id', 'service_types.id')
                    ->join('communities', 'all_maintenance_tickets.community_id', 'communities.id')
                    ->join('maintenance_types', 'all_maintenance_tickets.maintenance_type_id', 'maintenance_types.id')
                    ->join('maintenance_statuses', 'all_maintenance_tickets.maintenance_status_id', 'maintenance_statuses.id')
                    ->leftJoin('maintenance_status_reasons', 'all_maintenance_tickets.maintenance_status_reason_id', 'maintenance_status_reasons.id')
                    ->leftJoin('users', 'all_maintenance_tickets.assigned_to', 'users.id')
                    ->leftJoin('households', 'all_maintenance_tickets.comet_id', 'households.comet_id')
                    ->leftJoin('public_structures', 'all_maintenance_tickets.comet_id', 'public_structures.comet_id')
                    ->leftJoin('energy_systems', 'all_maintenance_tickets.comet_id', 'energy_systems.comet_id')
                    ->leftJoin('energy_generator_communities', 'all_maintenance_tickets.comet_id', 'energy_generator_communities.comet_id')
                    ->leftJoin('energy_turbine_communities', 'all_maintenance_tickets.comet_id', 'energy_turbine_communities.comet_id')
                    ->leftJoin('water_systems', 'all_maintenance_tickets.comet_id', 'water_systems.comet_id')
                    ->leftJoin('internet_systems', 'all_maintenance_tickets.comet_id', 'internet_systems.comet_id')
                    ->where('all_maintenance_tickets.is_archived', 0) 
                    ->where('all_maintenance_tickets.is_duplicated', 0);

                $data->when($agentFilter, fn($query) => $query
                        ->when($agentFilter == "household", fn($query) => $query->whereNotNull('households.comet_id'))
                        ->when($agentFilter == "public", fn($query) => $query->whereNotNull('public_structures.comet_id'))
                        ->when($agentFilter == "energy_system", fn($query) => $query->whereNotNull('energy_systems.comet_id'))
                        ->when($agentFilter == "water_system", fn($query) => $query->whereNotNull('water_systems.comet_id'))
                        ->when($agentFilter == "turbine", fn($query) => $query->whereNotNull('energy_turbine_communities.comet_id'))
                        ->when($agentFilter == "generator", fn($query) => $query->whereNotNull('energy_generator_communities.comet_id'))
                        ->when($agentFilter == "internet_system", fn($query) => $query->whereNotNull('internet_system.comet_id')))
                    ->when($communityFilter, fn($query) => $query->where('communities.id', $communityFilter))
                    ->when($serviceFilter, fn($query) => $query->where('service_types.id', $serviceFilter))
                    ->when($typeFilter, fn($query) => $query->where('maintenance_types.id', $typeFilter))
                    ->when($statusFilter, fn($query) => $query->where('maintenance_statuses.id', $statusFilter))
                    ->when($reasonFilter, fn($query) => $query->where('maintenance_status_reasons.id', $reasonFilter));

                $search = $request->input('search.value');

                if (!empty($search)) {
                    $data->where(function($w) use($search) {
                        // Apply the search to multiple columns
                        $w->orWhere('communities.english_name', 'LIKE', "%$search%")
                            ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('households.english_name', 'LIKE', "%$search%")
                            ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('public_structures.english_name', 'LIKE', "%$search%")
                            ->orWhere('public_structures.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('maintenance_statuses.name', 'LIKE', "%$search%")
                            ->orWhere('maintenance_types.type', 'LIKE', "%$search%")
                            ->orWhere('service_types.service_name', 'LIKE', "%$search%")
                            ->orWhere('energy_systems.name', 'LIKE', "%$search%")
                            ->orWhere('water_systems.name', 'LIKE', "%$search%")
                            ->orWhere('internet_system.system_name', 'LIKE', "%$search%")
                            ->orWhere('energy_turbine_communities.name', 'LIKE', "%$search%")
                            ->orWhere('energy_generator_communities.name', 'LIKE', "%$search%")
                            ->orWhere('users.name', 'LIKE', "%$search%");
                    });
                }

                $totalRecords = DB::table('all_maintenance_tickets')->where("is_archived", 0)->count();

                $filteredRecords = $data
                    ->when($agentFilter, fn($query) => $query
                        ->when($agentFilter == "household", fn($query) => $query->whereNotNull('households.comet_id'))
                        ->when($agentFilter == "public", fn($query) => $query->whereNotNull('public_structures.comet_id'))
                        ->when($agentFilter == "energy_system", fn($query) => $query->whereNotNull('energy_systems.comet_id'))
                        ->when($agentFilter == "water_system", fn($query) => $query->whereNotNull('water_systems.comet_id'))
                        ->when($agentFilter == "turbine", fn($query) => $query->whereNotNull('energy_turbine_communities.comet_id'))
                        ->when($agentFilter == "generator", fn($query) => $query->whereNotNull('energy_generator_communities.comet_id'))
                        ->when($agentFilter == "internet_system", fn($query) => $query->whereNotNull('internet_systems.comet_id')))
                    ->when($communityFilter, fn($query) => $query->where('communities.id', $communityFilter))
                    ->when($serviceFilter, fn($query) => $query->where('service_types.id', $serviceFilter))
                    ->when($typeFilter, fn($query) => $query->where('maintenance_types.id', $typeFilter))
                    ->when($statusFilter, fn($query) => $query->where('maintenance_statuses.id', $statusFilter))
                    ->when($reasonFilter, fn($query) => $query->where('maintenance_status_reasons.id', $reasonFilter))
                    ->count();

                $data = $data->select(
                        'all_maintenance_tickets.id as id', 
                        DB::raw('COALESCE(
                            households.english_name,
                            public_structures.english_name,
                            energy_systems.name,
                            energy_generator_communities.name,
                            energy_turbine_communities.name,
                            water_systems.name,
                            internet_systems.system_name
                        ) AS agent_name'),
                        'service_types.service_name',
                        'all_maintenance_tickets.start_date', 
                        'all_maintenance_tickets.completed_date', 'all_maintenance_tickets.notes',
                        'maintenance_types.type', 'maintenance_statuses.name', 
                        'maintenance_status_reasons.english_name as reason',
                        'communities.english_name as community_name',
                        'all_maintenance_tickets.created_at as created_at',
                        'all_maintenance_tickets.updated_at as updated_at',
                        'users.name as user_name'
                    )->latest()
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

            $communities = Community::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();

            $maintenanceTypes = MaintenanceType::where('is_archived', 0)->get();
            $maintenanceStatuses = MaintenanceStatus::where('is_archived', 0)->get();
            $maintenanceReasons = MaintenanceStatusReason::all();
            $users = User::where('is_archived', 0)->get();
            $serviceTypes = ServiceType::where('is_archived', 0)->get();

            return view('ticket.index', compact('communities', 'maintenanceTypes', 'maintenanceStatuses', 
                'serviceTypes', 'users', 'maintenanceReasons'));
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
        $allMaintenance = AllMaintenanceTicket::findOrFail($id);
        
        $household = null;
        $public = null;
        $energySystem = null;
        $waterSystem = null;
        $turbine = null;
        $generator = null;
        $waterActions = null;
        $energyActions = null;
        $internetActions = null;

        $household = Household::where("is_archived", 0)
            ->where("comet_id", $allMaintenance->comet_id)
            ->first();
        $public = PublicStructure::where("is_archived", 0)
            ->where("comet_id", $allMaintenance->comet_id)
            ->first();
        $energySystem = EnergySystem::where("is_archived", 0)
            ->where("comet_id", $allMaintenance->comet_id)
            ->first();
        $waterSystem = WaterSystem::where("comet_id", $allMaintenance->comet_id)->first();
        $turbine = EnergyTurbineCommunity::where("comet_id", $allMaintenance->comet_id)->first();
        $generator = EnergyGeneratorCommunity::where("comet_id", $allMaintenance->comet_id)->first();

        $community = Community::where("is_archived", 0)
            ->where('id', $allMaintenance->community_id)
            ->first();
        $serviceType = ServiceType::where("is_archived", 0)
            ->where('id', $allMaintenance->service_type_id)
            ->first();
        $status = MaintenanceStatus::where("is_archived", 0)
            ->where('id', $allMaintenance->maintenance_status_id)
            ->first();
        $type = MaintenanceType::where("is_archived", 0)
            ->where('id', $allMaintenance->maintenance_type_id)
            ->first();
        $user = User::where("is_archived", 0)
            ->where('id', $allMaintenance->assigned_to)
            ->first();

        if($allMaintenance->service_type_id == 1) {

            $energyActions = DB::table('all_maintenance_ticket_actions')
                ->join('all_maintenance_tickets', 'all_maintenance_tickets.id', 'all_maintenance_ticket_actions.all_maintenance_ticket_id')
                ->leftJoin('energy_issues', 'energy_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
                ->leftJoin('energy_actions', 'energy_issues.energy_action_id', 'energy_actions.id')
                ->leftJoin('action_categories as energy_categories', 'energy_categories.id', 'energy_actions.action_category_id')

                ->leftJoin('refrigerator_issues', 'refrigerator_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
                ->leftJoin('refrigerator_actions', 'refrigerator_issues.refrigerator_action_id', 'refrigerator_actions.id')
                ->leftJoin('action_categories as refrigerator_categories', 'refrigerator_categories.id', 'refrigerator_actions.action_category_id')

                ->where('all_maintenance_tickets.id', $allMaintenance->id)
                ->where('all_maintenance_ticket_actions.is_archived', 0)
                ->select(
                    'all_maintenance_ticket_actions.id',
                    DB::raw('IFNULL(energy_issues.english_name, refrigerator_issues.english_name) 
                        as issue_english_name'),
                    DB::raw('IFNULL(energy_issues.arabic_name, refrigerator_issues.arabic_name) 
                        as issue_arabic_name'),
                    DB::raw('IFNULL(energy_actions.english_name, refrigerator_actions.english_name) 
                        as action_english_name'),
                    DB::raw('IFNULL(energy_actions.arabic_name, refrigerator_actions.arabic_name) 
                        as action_arabic_name'),
                    DB::raw('IFNULL(energy_categories.english_name, refrigerator_categories.english_name) 
                        as category_english_name'),
                    DB::raw('IFNULL(energy_categories.arabic_name, refrigerator_categories.arabic_name) 
                        as category_arabic_name')
                )
                ->get();
        } else if($allMaintenance->service_type_id == 2) {

            $waterActions =  DB::table('all_maintenance_ticket_actions')
                ->join('all_maintenance_tickets', 'all_maintenance_tickets.id', 'all_maintenance_ticket_actions.all_maintenance_ticket_id')
                ->leftJoin('water_issues', 'water_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
                ->leftJoin('water_actions', 'water_issues.water_action_id', 'water_actions.id')
                ->leftJoin('action_categories as water_categories', 'water_categories.id', 'water_actions.action_category_id')
                ->where('all_maintenance_tickets.id', $allMaintenance->id)
                ->where('all_maintenance_ticket_actions.is_archived', 0)
                ->select(
                    'all_maintenance_ticket_actions.id',
                    'water_issues.english_name as issue_english_name',
                    'water_issues.arabic_name as issue_arabic_name',
                    'water_actions.english_name as action_english_name',
                    'water_actions.arabic_name as action_arabic_name',
                    'water_categories.english_name as category_english_name',
                    'water_categories.arabic_name as category_arabic_name'
                )
                ->get();
        } else if($allMaintenance->service_type_id == 3) {

            $internetActions =  DB::table('all_maintenance_ticket_actions')
                ->join('all_maintenance_tickets', 'all_maintenance_tickets.id', 'all_maintenance_ticket_actions.all_maintenance_ticket_id')
                ->leftJoin('internet_issues', 'internet_issues.comet_id', 'all_maintenance_ticket_actions.action_id')
                ->leftJoin('internet_actions', 'internet_issues.internet_action_id', 'internet_actions.id')
                ->leftJoin('action_categories as internet_categories', 'internet_categories.id', 'internet_actions.action_category_id')
                ->where('all_maintenance_tickets.id', $allMaintenance->id)
                ->where('all_maintenance_ticket_actions.is_archived', 0)
                ->select(
                    'all_maintenance_ticket_actions.id',
                    'internet_issues.english_name as issue_english_name',
                    'internet_issues.arabic_name as issue_arabic_name',
                    'internet_actions.english_name as action_english_name',
                    'internet_actions.arabic_name as action_arabic_name',
                    'internet_categories.english_name as category_english_name',
                    'internet_categories.arabic_name as category_arabic_name'
                )
                ->get();
        }
    
        $response['community'] = $community;
        $response['allMaintenance'] = $allMaintenance;
        $response['serviceType'] = $serviceType;
        $response['energyActions'] = $energyActions;
        $response['waterActions'] = $waterActions;
        $response['internetActions'] = $internetActions;
        $response['status'] = $status;
        $response['type'] = $type;
        $response['user'] = $user;
        $response['household'] = $household;
        $response['public'] = $public;
        $response['energySystem'] = $energySystem;
        $response['waterSystem'] = $waterSystem;
        $response['turbine'] = $turbine;
        $response['generator'] = $generator;

        return response()->json($response);
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request) 
    {
                
        return Excel::download(new AllMaintenanceExport($request), 'all_maintenances.xlsx');
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {
        Excel::import(new ImportEnergyMaintenance, $request->file('file')); 
            
        return back()->with('success', 'Excel Data Imported successfully.');
    }
}