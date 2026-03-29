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
use App\Models\Donor;
use App\Models\Community;
use App\Models\Compound;
use App\Models\CommunityService;
use App\Models\CameraCommunityType;
use App\Models\CameraCommunity;
use App\Models\CameraCommunityDonor;
use App\Models\CameraReplacementIncident;
use App\Models\NvrCommunityType;
use App\Models\CameraCommunityPhoto;
use App\Models\Camera;
use App\Models\NvrCamera;
use App\Models\Region; 
use App\Models\Household; 
use App\Models\SubRegion;
use App\Models\Repository;
use App\Exports\CameraCommunityExport;
use Carbon\Carbon;
use DataTables;
use Excel;
use Illuminate\Support\Facades\URL;

class CameraCommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        // $cameraCommunities = CameraCommunity::where('is_archived', 0)->get();

        // foreach($cameraCommunities as $cameraCommunity) {

        //     $cameraCommunityDonor = new CameraCommunityDonor();
        //     $cameraCommunityDonor->camera_community_id = $cameraCommunity->id;
        //     $cameraCommunityDonor->donor_id = 2;
        //     $cameraCommunityDonor->save();

        // }

        // die( $cameraCommunities );
        $cameraReplacementIncidents = CameraReplacementIncident::all();
        $allCommunityCameras = CameraCommunity::where('is_archived', 0)
            ->where('community_id', '!=', NULL)
            ->get();

        // Only update community service info when not responding to DataTables AJAX
        if (! $request->ajax()) {
            foreach($allCommunityCameras as $allCommunityCamera) {
                try {
                    $communityService = CommunityService::firstOrCreate(
                        ['community_id' => $allCommunityCamera->community_id, 'service_id' => 4]
                    );

                    if ($allCommunityCamera->date) {
                        $dateTime = Carbon::createFromFormat('Y-m-d', $allCommunityCamera->date);
                        $year = $dateTime->year;
                        $community = Community::findOrFail($allCommunityCamera->community_id);
                        $community->camera_service = "Yes";
                        $community->camera_service_beginning_year = $year;
                        $community->save();
                    }
                } catch (\Exception $e) {
                    // swallow conversion/format errors during background processing
                    continue;
                }
            }
        }

        if (Auth::guard('user')->user() != null) {

            $communities = Community::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();

            $communityFilter = $request->input('community_filter');
            $regionFilter = $request->input('region_filter');
            $dateFilter = $request->input('date_filter');

            if ($request->ajax()) {
                $data = DB::table('camera_communities')
                    ->leftJoin('communities', 'camera_communities.community_id', 'communities.id')
                    ->leftJoin('regions', 'communities.region_id', 'regions.id')
                    ->leftJoin('sub_regions', 'communities.region_id', 'regions.id')
                    ->leftJoin('households', 'camera_communities.household_id', 'households.id')
                    ->leftJoin('repositories', 'camera_communities.repository_id', 'repositories.id')
                    ->leftJoin('regions as repository_regions', 'repositories.region_id', 
                        'repository_regions.id')
                    ->leftJoin('camera_community_types', 'camera_communities.id', 
                        'camera_community_types.camera_community_id')
                    ->leftJoin('nvr_community_types', 'camera_communities.id', 
                        'nvr_community_types.camera_community_id')
                    ->leftJoin('compounds', 'camera_communities.compound_id', 'compounds.id')
                    ->where('camera_communities.is_archived', 0);

                $requestedData = DB::table('requested_cameras')
                    ->join('communities', 'requested_cameras.community_id', 'communities.id')
                    ->join('regions', 'communities.region_id', 'regions.id')
                    ->join('camera_request_statuses', 'requested_cameras.camera_request_status_id', 'camera_request_statuses.id')
                    ->where('requested_cameras.is_archived', 0);

                if ($communityFilter != null) {
                    $data->where('communities.id', $communityFilter);
                    $requestedData->where('communities.id', $communityFilter);
                }
                if ($regionFilter != null) {
                    $data->where('regions.id', $regionFilter);
                    $requestedData->where('regions.id', $regionFilter);
                }
                if ($dateFilter != null) {
                    $data->where('camera_communities.date', '>=', $dateFilter);
                }

                $data->select(
                    DB::raw('IFNULL(communities.english_name, repositories.name) as name'), 
                    'households.english_name',
                    'camera_communities.id as id', 'camera_communities.created_at as created_at', 
                    'camera_communities.updated_at as updated_at', 'camera_communities.date as installation_date',
                    DB::raw('IFNULL(regions.english_name, repository_regions.english_name) as region'),
                    // installed cameras (sum of camera_community_types.number)
                    DB::raw('(SELECT COALESCE(SUM(number),0) FROM camera_community_types WHERE camera_community_types.camera_community_id = camera_communities.id) as installed_count'),
                    // replaced, added, returned counts per camera_community
                    DB::raw('(SELECT COALESCE(SUM(new_camera_count),0) FROM camera_community_replacements WHERE camera_community_replacements.camera_community_id = camera_communities.id) as replaced_count'),
                    DB::raw('(SELECT COALESCE(SUM(number_of_cameras),0) FROM camera_community_additions WHERE camera_community_additions.camera_community_id = camera_communities.id) as added_count'),
                    DB::raw('(SELECT COALESCE(SUM(number_of_cameras),0) FROM camera_community_returned WHERE camera_community_returned.camera_community_id = camera_communities.id) as returned_count'),
                    // damaged count: correlate incidents by community_id or repository_id
                    DB::raw('(SELECT COALESCE(SUM(acide.count),0) FROM all_camera_incident_damaged_equipment acide JOIN all_camera_incidents aci ON acide.all_camera_incident_id = aci.id WHERE acide.incident_equipment_id = 31 AND (aci.community_id = camera_communities.community_id OR aci.repository_id = camera_communities.repository_id)) as damaged_count'),
                    // total_current = installed + added - damaged + replaced - returned
                    DB::raw('((SELECT COALESCE(SUM(number),0) FROM camera_community_types WHERE camera_community_types.camera_community_id = camera_communities.id) + (SELECT COALESCE(SUM(number_of_cameras),0) FROM camera_community_additions WHERE camera_community_additions.camera_community_id = camera_communities.id) - (SELECT COALESCE(SUM(acide.count),0) FROM all_camera_incident_damaged_equipment acide JOIN all_camera_incidents aci ON acide.all_camera_incident_id = aci.id WHERE acide.incident_equipment_id = 31 AND (aci.community_id = camera_communities.community_id OR aci.repository_id = camera_communities.repository_id)) + (SELECT COALESCE(SUM(new_camera_count),0) FROM camera_community_replacements WHERE camera_community_replacements.camera_community_id = camera_communities.id) - (SELECT COALESCE(SUM(number_of_cameras),0) FROM camera_community_returned WHERE camera_community_returned.camera_community_id = camera_communities.id)) as total_current'),
                    DB::raw('SUM(DISTINCT nvr_community_types.number) as nvr_number'),
                    'compounds.english_name as compound'
                )->groupBy('camera_communities.id')->latest();

                try {
                    return Datatables::of($data)
                        ->addIndexColumn()
                        ->addColumn('action', function($row) {
                            $detailsButton = "<a type='button' class='viewCameraCommunityButton' data-id='".$row->id."'><i class='fa-solid fa-eye text-primary'></i></a>";
                            $updateButton = "<a type='button' class='updateCameraCommunity' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
                            $deleteButton = "<a type='button' class='deleteCameraCommunity' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";

                            if (Auth::guard('user')->user()->user_type_id != 7 || 
                                Auth::guard('user')->user()->user_type_id != 11 || 
                                Auth::guard('user')->user()->user_type_id != 8) {
                                return $detailsButton." ". $updateButton." ".$deleteButton;
                            }
                            return $detailsButton;
                        })
                        ->filter(function ($instance) use ($request) {
                            $rawSearch = $request->get('search');
                            $search = '';
                            if (is_array($rawSearch)) {
                                $search = $rawSearch['value'] ?? '';
                            } else {
                                $search = $rawSearch;
                            }
                            if ($search !== '') {
                                $instance->where(function($w) use ($search) {
                                    $w->orWhere('households.english_name', 'LIKE', "%$search%")
                                    ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                                    ->orWhere('communities.english_name', 'LIKE', "%$search%")
                                    ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                                    ->orWhere('regions.arabic_name', 'LIKE', "%$search%")
                                    ->orWhere('regions.english_name', 'LIKE', "%$search%")
                                    ->orWhere('repositories.name', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->rawColumns(['action'])
                        ->make(true);
                } catch (\Exception $e) {
                    $msg = $e->getMessage();
                    return response()->json([
                        'error' => 'DataTables processing error',
                        'message' => $msg,
                        'trace' => $e->getTraceAsString()
                    ], 500);
                }
            }

            $donors = Donor::where('is_archived', 0)->get();
            $regions = Region::where('is_archived', 0)->get();
            $subRegions = SubRegion::where('is_archived', 0)->get();
            $cameras = Camera::all();
            $nvrCameras = NvrCamera::all();
            $nvrs = NvrCamera::all();
            $repositories = Repository::all();
            $compounds = Compound::where('is_archived', 0)->get();

            return view('services.camera.index', compact('communities', 'cameras', 'subRegions',
                'nvrCameras', 'repositories', 'regions', 'compounds'));

        } else {

            return view('errors.not-found');
        }
    }

    /**
     * Aggregated view showing Installed, Replacements and Additions tables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        if (Auth::guard('user')->user() != null) {
            $communities = Community::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();

            $donors = Donor::where('is_archived', 0)->get();
            $regions = Region::where('is_archived', 0)->get();
            $subRegions = SubRegion::where('is_archived', 0)->get();
            $cameras = Camera::all();
            $nvrCameras = NvrCamera::all();
            $nvrs = NvrCamera::all();
            $repositories = Repository::all();
            $compounds = Compound::where('is_archived', 0)->get();
            $cameraReplacementIncidents = CameraReplacementIncident::all();
            $cameraCommunities = CameraCommunity::with('community')->get();

            // Summary totals
            $total_installed = DB::table('camera_community_types')
                ->join('camera_communities', 'camera_community_types.camera_community_id', '=', 'camera_communities.id')
                ->where('camera_communities.is_archived', 0)
                ->sum(DB::raw('COALESCE(camera_community_types.number,0)'));

            $total_replaced = DB::table('camera_community_replacements')
                ->sum(DB::raw('COALESCE(new_camera_count,0)'));

            $total_added = DB::table('camera_community_additions')
                ->sum(DB::raw('COALESCE(number_of_cameras,0)'));

            $total_returned = DB::table('camera_community_returned')
                            ->leftJoin('camera_communities', 'camera_community_returned.camera_community_id', '=', 'camera_communities.id')
                ->where('camera_communities.is_archived', 0)

                ->sum(DB::raw('COALESCE(number_of_cameras,0)'));

            // Damaged cameras are recorded in incident damaged equipment
            // Use the incidents damaged equipment table where incident_equipment_id=31 (cameras)
            $total_damaged = DB::table('all_camera_incident_damaged_equipment')
                ->where('incident_equipment_id', 31)
                ->sum(DB::raw('COALESCE(count,0)'));

            // Current total: installed + added - damaged + replaced - returned
            $total_current = ((int) $total_installed) + ((int) $total_added) - ((int) $total_damaged) + ((int) $total_replaced) - ((int) $total_returned);


$total_served_communities = DB::table('camera_communities')
                ->where('is_archived', 0)
                ->whereNotNull('community_id')
                ->distinct()
                ->count('community_id');
            // Total cameras installed under repository records (repository_id not null)
            $total_repository_cameras = DB::table('camera_community_types')
                ->join('camera_communities', 'camera_community_types.camera_community_id', '=', 'camera_communities.id')
                ->whereNotNull('camera_communities.repository_id')
                ->where('camera_communities.is_archived', 0)
                ->sum(DB::raw('COALESCE(camera_community_types.number,0)'));

            // Count of distinct repositories that have at least one camera record
            $total_served_repositories = DB::table('camera_communities')
                ->where('is_archived', 0)
                ->whereNotNull('repository_id')
                ->distinct()
                ->count('repository_id');

                
            $summaryData = [
                'total_installed' => (int) $total_installed,
                'total_replaced' => (int) $total_replaced,
                'total_added' => (int) $total_added,
                'total_returned' => (int) $total_returned,
                'total_damaged' => (int) $total_damaged,
                                'total_served_communities' => (int) $total_served_communities,
                'total_repository_cameras' => (int) $total_repository_cameras,
                'total_served_repositories' => (int) $total_served_repositories,

                'total_current' => (int) $total_current,
            ];

            // Per-region and per-community breakdowns
            $installedByCommunity = DB::table('camera_community_types')
                ->join('camera_communities', 'camera_community_types.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('communities', 'camera_communities.community_id', '=', 'communities.id')
                ->leftJoin('regions', 'communities.region_id', '=', 'regions.id')
                ->where('camera_communities.is_archived', 0)
                ->select('regions.english_name as region', 'communities.english_name as community', DB::raw('COALESCE(SUM(camera_community_types.number),0) as installed'))
                ->groupBy('regions.english_name', 'communities.english_name')
                ->get();

            $replacedByCommunity = DB::table('camera_community_replacements')
                ->leftJoin('camera_communities', 'camera_community_replacements.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('communities', 'camera_communities.community_id', '=', 'communities.id')
                ->leftJoin('regions', 'communities.region_id', '=', 'regions.id')
                ->select('regions.english_name as region', 'communities.english_name as community', DB::raw('COALESCE(SUM(camera_community_replacements.new_camera_count),0) as replaced'))
                ->groupBy('regions.english_name', 'communities.english_name')
                ->get();

            $addedByCommunity = DB::table('camera_community_additions')
                ->leftJoin('camera_communities', 'camera_community_additions.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('communities', 'camera_communities.community_id', '=', 'communities.id')
                ->leftJoin('regions', 'communities.region_id', '=', 'regions.id')
                ->select('regions.english_name as region', 'communities.english_name as community', DB::raw('COALESCE(SUM(camera_community_additions.number_of_cameras),0) as added'))
                ->groupBy('regions.english_name', 'communities.english_name')
                ->get();

            $returnedByCommunity = DB::table('camera_community_returned')
                ->leftJoin('camera_communities', 'camera_community_returned.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('communities', 'camera_communities.community_id', '=', 'communities.id')
                ->leftJoin('regions', 'communities.region_id', '=', 'regions.id')
                ->select('regions.english_name as region', 'communities.english_name as community', DB::raw('COALESCE(SUM(camera_community_returned.number_of_cameras),0) as returned'))
                ->groupBy('regions.english_name', 'communities.english_name')
                ->get();

            // Repository-level aggregations (repositories act like communities when repository_id is set)
            $installedByRepository = DB::table('camera_community_types')
                ->join('camera_communities', 'camera_community_types.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('repositories', 'camera_communities.repository_id', '=', 'repositories.id')
                ->leftJoin('regions', 'repositories.region_id', '=', 'regions.id')
                ->where('camera_communities.is_archived', 0)
                ->whereNotNull('camera_communities.repository_id')
                ->select('regions.english_name as region', 'repositories.name as community', DB::raw('COALESCE(SUM(camera_community_types.number),0) as installed'))
                ->groupBy('regions.english_name', 'repositories.name')
                ->get();

            $replacedByRepository = DB::table('camera_community_replacements')
                ->leftJoin('camera_communities', 'camera_community_replacements.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('repositories', 'camera_communities.repository_id', '=', 'repositories.id')
                ->leftJoin('regions', 'repositories.region_id', '=', 'regions.id')
                ->whereNotNull('camera_communities.repository_id')
                ->select('regions.english_name as region', 'repositories.name as community', DB::raw('COALESCE(SUM(camera_community_replacements.new_camera_count),0) as replaced'))
                ->groupBy('regions.english_name', 'repositories.name')
                ->get();

            $addedByRepository = DB::table('camera_community_additions')
                ->leftJoin('camera_communities', 'camera_community_additions.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('repositories', 'camera_communities.repository_id', '=', 'repositories.id')
                ->leftJoin('regions', 'repositories.region_id', '=', 'regions.id')
                ->whereNotNull('camera_communities.repository_id')
                ->select('regions.english_name as region', 'repositories.name as community', DB::raw('COALESCE(SUM(camera_community_additions.number_of_cameras),0) as added'))
                ->groupBy('regions.english_name', 'repositories.name')
                ->get();

            $returnedByRepository = DB::table('camera_community_returned')
                ->leftJoin('camera_communities', 'camera_community_returned.camera_community_id', '=', 'camera_communities.id')
                ->leftJoin('repositories', 'camera_communities.repository_id', '=', 'repositories.id')
                ->leftJoin('regions', 'repositories.region_id', '=', 'regions.id')
                ->whereNotNull('camera_communities.repository_id')
                ->select('regions.english_name as region', 'repositories.name as community', DB::raw('COALESCE(SUM(camera_community_returned.number_of_cameras),0) as returned'))
                ->groupBy('regions.english_name', 'repositories.name')
                ->get();

            // Merge results into a structure keyed by region, with nested communities
            $byRegion = [];

            // Helper to ensure region exists
            $ensureRegion = function ($regionName) use (&$byRegion) {
                $key = $regionName ?? 'Unspecified';
                if (!isset($byRegion[$key])) {
                    $byRegion[$key] = [
                        'region' => $key,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0,
                        'communities' => []
                    ];
                }
                return $key;
            };

            foreach ($installedByCommunity as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_installed'] = (int) $r->installed;
                $byRegion[$regionKey]['total_installed'] += (int) $r->installed;
            }

            foreach ($replacedByCommunity as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_replaced'] = (int) $r->replaced;
                $byRegion[$regionKey]['total_replaced'] += (int) $r->replaced;
            }

            foreach ($addedByCommunity as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_added'] = (int) $r->added;
                $byRegion[$regionKey]['total_added'] += (int) $r->added;
            }

            // Merge repository-level aggregates into the same structure
            foreach ($installedByRepository as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_installed'] = (int) $r->installed;
                $byRegion[$regionKey]['total_installed'] += (int) $r->installed;
            }

            foreach ($replacedByRepository as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_replaced'] = (int) $r->replaced;
                $byRegion[$regionKey]['total_replaced'] += (int) $r->replaced;
            }

            foreach ($addedByRepository as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_added'] = (int) $r->added;
                $byRegion[$regionKey]['total_added'] += (int) $r->added;
            }

            foreach ($returnedByRepository as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_returned'] = (int) $r->returned;
                $byRegion[$regionKey]['total_returned'] += (int) $r->returned;
            }

            foreach ($returnedByCommunity as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_returned'] = (int) $r->returned;
                $byRegion[$regionKey]['total_returned'] += (int) $r->returned;
            }

            // Damaged by community comes from incident damaged equipment records
            // Join incident damaged equipment -> all_camera_incidents -> communities/regions
            $damagedByCommunity = DB::table('all_camera_incident_damaged_equipment')
                ->leftJoin('all_camera_incidents', 'all_camera_incident_damaged_equipment.all_camera_incident_id', '=', 'all_camera_incidents.id')
                ->leftJoin('communities', 'all_camera_incidents.community_id', '=', 'communities.id')
                ->leftJoin('regions', 'communities.region_id', '=', 'regions.id')
                ->where('all_camera_incident_damaged_equipment.incident_equipment_id', 31)
                ->select('regions.english_name as region', 'communities.english_name as community', DB::raw('COALESCE(SUM(all_camera_incident_damaged_equipment.count),0) as damaged'))
                ->groupBy('regions.english_name', 'communities.english_name')
                ->get();

            // Damaged by repository (incident records linked to repositories)
            $damagedByRepository = DB::table('all_camera_incident_damaged_equipment')
                ->leftJoin('all_camera_incidents', 'all_camera_incident_damaged_equipment.all_camera_incident_id', '=', 'all_camera_incidents.id')
                ->leftJoin('repositories', 'all_camera_incidents.repository_id', '=', 'repositories.id')
                ->leftJoin('regions', 'repositories.region_id', '=', 'regions.id')
                ->where('all_camera_incident_damaged_equipment.incident_equipment_id', 31)
                ->select('regions.english_name as region', 'repositories.name as community', DB::raw('COALESCE(SUM(all_camera_incident_damaged_equipment.count),0) as damaged'))
                ->groupBy('regions.english_name', 'repositories.name')
                ->get();

            foreach ($damagedByCommunity as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_damaged'] = (int) $r->damaged;
                $byRegion[$regionKey]['total_damaged'] += (int) $r->damaged;
            }

            // Merge damaged counts from repositories into the structure
            foreach ($damagedByRepository as $r) {
                $regionKey = $ensureRegion($r->region);
                $communityName = $r->community ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0,
                        'total_damaged' => 0,
                        'total_current' => 0
                    ];
                }
                $byRegion[$regionKey]['communities'][$communityName]['total_damaged'] = (int) $r->damaged;
                $byRegion[$regionKey]['total_damaged'] += (int) $r->damaged;
            }

            // Ensure every community appears under its region (even with zero counts)
            $regionNamesById = [];
            foreach ($regions as $r) {
                $regionNamesById[$r->id] = $r->english_name;
            }

            foreach ($communities as $comm) {
                $regionName = $regionNamesById[$comm->region_id] ?? 'Unspecified';
                $regionKey = $ensureRegion($regionName);
                $communityName = $comm->english_name ?? 'Unspecified';
                if (!isset($byRegion[$regionKey]['communities'][$communityName])) {
                    $byRegion[$regionKey]['communities'][$communityName] = [
                        'community' => $communityName,
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_returned' => 0
                    ];
                }
            }

            // Ensure each community entry has all expected numeric keys (including damaged/current)
            foreach ($byRegion as $regionKey => $regionData) {
                foreach ($regionData['communities'] as $commKey => $commData) {
                    $byRegion[$regionKey]['communities'][$commKey] = array_merge([
                        'community' => $commData['community'] ?? 'Unspecified',
                        'total_installed' => 0,
                        'total_replaced' => 0,
                        'total_added' => 0,
                        'total_damaged' => 0,
                        'total_returned' => 0,
                        'total_current' => 0
                    ], $commData);
                }
            }

            // Convert communities from associative to indexed arrays for JSON friendliness
            // Compute per-community and per-region 'current' totals
            foreach ($byRegion as $regionKey => $regionData) {
                foreach ($regionData['communities'] as $commKey => $commData) {
                    $installed = $commData['total_installed'] ?? 0;
                    $added = $commData['total_added'] ?? 0;
                    $damaged = $commData['total_damaged'] ?? 0;
                    $replaced = $commData['total_replaced'] ?? 0;
                    $returned = $commData['total_returned'] ?? 0;
                    $current = ((int)$installed) + ((int)$added) - ((int)$damaged) + ((int)$replaced) - ((int)$returned);
                    $byRegion[$regionKey]['communities'][$commKey]['total_current'] = (int)$current;
                }

                // region current is sum of community currents
                $regionCurrent = 0;
                foreach ($byRegion[$regionKey]['communities'] as $c) {
                    $regionCurrent += (int) ($c['total_current'] ?? 0);
                }
                $byRegion[$regionKey]['total_current'] = (int) $regionCurrent;
            }

            foreach ($byRegion as $k => $v) {
                $byRegion[$k]['communities'] = array_values($v['communities']);
            }

            // Remove entries for 'Unspecified' region — not needed in summary output
            if (isset($byRegion['Unspecified'])) {
                unset($byRegion['Unspecified']);
            }

            $summaryByRegion = array_values($byRegion);

            return view('services.camera.all_cameras', compact(
                'communities', 'donors', 'regions', 'subRegions', 'cameras',
                'nvrCameras', 'nvrs', 'repositories', 'compounds', 'cameraReplacementIncidents', 'cameraCommunities',
                'summaryData', 'summaryByRegion'
            ));
        }

        return view('errors.not-found');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        $validatedData = $request->validate([
            'camera_id.*' => 'nullable',
            'addMoreInputFieldsCameraNumber.*.subject' => 'required',
            'addMoreInputFieldsSdCard.*.subject' => 'nullable',
            'addMoreInputFieldsCameraBaseNumber.*.subject' => 'nullable',
            'addMoreInputFieldsInternetCableNumber.*.subject' => 'nullable',
            // NVR fields are optional; only validate if provided by the frontend
            'addMoreInputFieldsNvrNumber.*.subject' => 'nullable',
            'addMoreInputFieldsNvrIpAddress.*.subject' => 'nullable',
            'compound_id' => 'nullable|exists:compounds,id',
            'ci4' => 'nullable|numeric|min:0',
            'camera_accessories_number' => 'nullable|string|max:255',
            'electricity_cable_number' => 'nullable|numeric|min:0',
        ]);

        // If a '# of Camera' is provided for a row, require the corresponding camera model
        $numbers = $request->input('addMoreInputFieldsCameraNumber', []);
        $cameraIds = $request->input('camera_id', []);
        foreach ($numbers as $index => $numArr) {
            $num = isset($numArr['subject']) ? trim($numArr['subject']) : '';
            if ($num !== '' && (empty($cameraIds[$index]) || $cameraIds[$index] == null)) {
                return redirect()->back()->withInput()->withErrors(['camera_id.' . $index => 'Camera Model is required when # of Camera is provided.']);
            }
        }

        $cameraCommunity = new CameraCommunity();

        if($request->community_id) {

            $cameraCommunity->community_id = $request->community_id;
            $community = Community::findOrFail($request->community_id);
            $community->camera_service = "Yes";
            if($request->date) $year = Carbon::parse($request->date)->year;
            $community->camera_service_beginning_year = $year;
            $community->save();

            $communityService = CommunityService::firstOrCreate(
                ['community_id' =>  $request->community_id, 'service_id' => 4]
            );
        } else if($request->repository_id ) {

            $cameraCommunity->repository_id = $request->repository_id;
            $cameraCommunity->comet_internal = 1;
        }
       
        $cameraCommunity->date = $request->date;
        $cameraCommunity->notes = $request->notes;
        if($request->household_id) $cameraCommunity->household_id = $request->household_id;
        // optional fields: compound, ci4, camera accessories, electricity cable length
        if($request->has('compound_id')) $cameraCommunity->compound_id = $request->input('compound_id');
        if($request->has('ci4')) $cameraCommunity->ci4 = $request->input('ci4');
        if($request->has('camera_accessories_number')) $cameraCommunity->camera_accessories_number = $request->input('camera_accessories_number');
        if($request->has('electricity_cable_number')) $cameraCommunity->electricity_cable_number = $request->input('electricity_cable_number');
        $cameraCommunity->save();

        foreach ($validatedData['camera_id'] as $index => $cameraId) {
            $cameraCommunityType = new CameraCommunityType(); 
            $cameraCommunityType->camera_id = $cameraId;
            $cameraCommunityType->camera_community_id = $cameraCommunity->id;
            $cameraCommunityType->number = $validatedData['addMoreInputFieldsCameraNumber'][$index]['subject'];
            $cameraCommunityType->sd_card_number = isset($validatedData['addMoreInputFieldsSdCard'][$index]['subject']) ? $validatedData['addMoreInputFieldsSdCard'][$index]['subject'] : null;
            $cameraCommunityType->camera_base_number = isset($validatedData['addMoreInputFieldsCameraBaseNumber'][$index]['subject']) ? $validatedData['addMoreInputFieldsCameraBaseNumber'][$index]['subject'] : null;
            $cameraCommunityType->internet_cable_number = isset($validatedData['addMoreInputFieldsInternetCableNumber'][$index]['subject']) ? $validatedData['addMoreInputFieldsInternetCableNumber'][$index]['subject'] : null;
            $cameraCommunityType->save();
        }

        // Handle optional NVR entries if provided by the frontend. Use the explicit
        // `nvr_id` input (if present) and corresponding number/ip arrays. If no
        // NVRs were submitted, this loop will be skipped and storing still succeeds.
        $nvrIds = $request->input('nvr_id', []);
        $nvrNumbers = $request->input('addMoreInputFieldsNvrNumber', []);
        $nvrIps = $request->input('addMoreInputFieldsNvrIpAddress', []);

        if (!empty($nvrIds) && is_array($nvrIds)) {
            foreach ($nvrIds as $index => $nvrId) {
                if (empty($nvrId)) continue;

                $nvrCommunityType = new NvrCommunityType();
                $nvrCommunityType->nvr_camera_id = $nvrId;
                $nvrCommunityType->camera_community_id = $cameraCommunity->id;
                $nvrCommunityType->ip_address = isset($nvrIps[$index]['subject']) ? $nvrIps[$index]['subject'] : null;
                $nvrCommunityType->number = isset($nvrNumbers[$index]['subject']) ? $nvrNumbers[$index]['subject'] : null;
                $nvrCommunityType->save();
            }
        }

        if ($request->file('photos')) { 

            foreach($request->photos as $photo) {

                $original_name = $photo->getClientOriginalName();
                $extra_name  = uniqid().'_'.time().'_'.uniqid().'.'.$photo->extension();
                $encoded_base64_image = substr($photo, strpos($photo, ',') + 1); 
                $destinationPath = public_path().'/cameras/community/' ;
                $photo->move($destinationPath, $extra_name);
    
                $cameraCommunityPhoto = new CameraCommunityPhoto();
                $cameraCommunityPhoto->slug = $extra_name;
                $cameraCommunityPhoto->camera_community_id = $cameraCommunity->id;
                $cameraCommunityPhoto->save();
            }
        }

        return redirect()->back()->with('message', 'New Installed Cameras Inserted Successfully!');
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response 
     */
    public function show($id)
    {
        $sharedHouseholds = [];
        $cameraCommunity = CameraCommunity::findOrFail($id);
        $cameraCommunityTypes = CameraCommunityType::where("camera_community_id", $id)->get();
        $nvrCommunityTypes = NvrCommunityType::where("camera_community_id", $id)->get();

        $cameraPhotos = CameraCommunityPhoto::where('camera_community_id', $id)
            ->get();

        $cameraDonors = CameraCommunityDonor::where("camera_community_id", $id)
            ->where("is_archived", 0)
            ->get();

        // load additions (history of added cameras) for this community
        $cameraAdditions = \App\Models\CameraCommunityAddition::where('camera_community_id', $id)
            ->with(['donors', 'camera', 'nvrCamera'])
            ->orderBy('date_of_addition', 'DESC')
            ->get();

        // compute per-community totals for details view
        $installedCount = 0;
        if(isset($cameraCommunityTypes) && count($cameraCommunityTypes) > 0) {
            foreach($cameraCommunityTypes as $ct) {
                $installedCount += intval($ct->number ?? 0);
            }
        }

        $addedCount = $cameraAdditions->sum('number_of_cameras');

        $replacedCount = DB::table('camera_community_replacements')
            ->where('camera_community_id', $id)
            ->sum('new_camera_count');

        $returnedCount = DB::table('camera_community_returned')
            ->where('camera_community_id', $id)
            ->sum('number_of_cameras');

        // damaged from incidents table
        $damagedQuery = DB::table('all_camera_incident_damaged_equipment')
            ->leftJoin('all_camera_incidents', 'all_camera_incident_damaged_equipment.all_camera_incident_id', '=', 'all_camera_incidents.id')
            ->where('all_camera_incident_damaged_equipment.incident_equipment_id', 31);

        // all_camera_incidents references community_id and repository_id (not camera_community_id)
        if (!empty($cameraCommunity->community_id)) {
            $damagedQuery->where('all_camera_incidents.community_id', $cameraCommunity->community_id);
        } elseif (!empty($cameraCommunity->repository_id)) {
            $damagedQuery->where('all_camera_incidents.repository_id', $cameraCommunity->repository_id);
        } else {
            // no link to community or repository; no damaged incidents can be associated
            $damagedCount = 0;
        }

        if (!isset($damagedCount)) {
            $damagedCount = $damagedQuery->sum('all_camera_incident_damaged_equipment.count');
        }

        $totalCurrentDetails = ($installedCount + $addedCount - $damagedCount + $replacedCount - $returnedCount);

        return view('services.camera.show', compact('cameraCommunity', 'nvrCommunityTypes', 
            'sharedHouseholds', 'cameraPhotos', 'cameraCommunityTypes', 'cameraDonors', 'cameraAdditions',
            'installedCount', 'addedCount', 'replacedCount', 'returnedCount', 'damagedCount', 'totalCurrentDetails'));
    }


     /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function editPage($id)
    {
        $cameraCommunity = CameraCommunity::findOrFail($id);

        return response()->json($cameraCommunity);
    } 

    /** 
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $communities = Community::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->get();
        $cameraCommunity = CameraCommunity::findOrFail($id);
        $households = Household::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->where("community_id", $cameraCommunity->community_id)
            ->get();

        $cameras = Camera::all();
        $nvrCameras = NvrCamera::all();
        $donors = Donor::where('is_archived', 0)->get();

        $cameraDonorsId = CameraCommunityDonor::where("camera_community_id", $id)
            ->where("is_archived", 0)
            ->pluck('donor_id'); 

        $moreDonors = Donor::where('is_archived', 0)
            ->whereNotIn('id', $cameraDonorsId) 
            ->get();

        $cameraDonors = CameraCommunityDonor::where("camera_community_id", $id)
            ->where("is_archived", 0)
            ->get();
        $communityCameraTypes = CameraCommunityType::where("camera_community_id", $id)->get();
        $communityNvrTypes = NvrCommunityType::where("camera_community_id", $id)->get();
        $cameraCommunityPhotos = CameraCommunityPhoto::where("camera_community_id", $id)->get();
        $compounds = Compound::where('is_archived', 0)->get();
            
        return view('services.camera.edit', compact('communities', 'cameras', 'donors',
            'cameraCommunity', 'nvrCameras', 'households', 'communityCameraTypes', 'compounds',
            'communityNvrTypes', 'cameraCommunityPhotos', 'cameraDonors', 'moreDonors'));
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validate optional numeric/text fields
        $request->validate([
            'compound_id' => 'nullable|exists:compounds,id',
            'ci4' => 'nullable|numeric|min:0',
            'camera_accessories_number' => 'nullable|string|max:255',
            'electricity_cable_number' => 'nullable|numeric|min:0',
        ]);

        $cameraCommunity = CameraCommunity::findOrFail($id);
        if($request->household_id) $cameraCommunity->household_id = $request->household_id;
        if($request->date == null) $cameraCommunity->date = null;
        if($request->date) $cameraCommunity->date = $request->date;
        if($request->comet_internal) $cameraCommunity->comet_internal = 1;
        if($request->notes) $cameraCommunity->notes = $request->notes;
        // save compound and additional camera fields if provided
        if($request->has('compound_id')) $cameraCommunity->compound_id = $request->input('compound_id');
        if($request->has('ci4')) $cameraCommunity->ci4 = $request->input('ci4');
        if($request->has('camera_accessories_number')) $cameraCommunity->camera_accessories_number = $request->input('camera_accessories_number');
        if($request->has('electricity_cable_number')) $cameraCommunity->electricity_cable_number = $request->input('electricity_cable_number');
        $cameraCommunity->save();

        if($request->camera_id) {

            $validatedData = $request->validate([
                'camera_id.*' => 'nullable',
                'addMoreInputFieldsCameraNumber.*.subject' => 'required',
                'addMoreInputFieldsSdCard.*.subject' => 'nullable',
                'addMoreInputFieldsCameraBaseNumber.*.subject' => 'nullable',
                'addMoreInputFieldsInternetCableNumber.*.subject' => 'nullable',
            ]);

            // enforce camera model selection when number is provided
            $numbers = $request->input('addMoreInputFieldsCameraNumber', []);
            $cameraIds = $request->input('camera_id', []);
            foreach ($numbers as $index => $numArr) {
                $num = isset($numArr['subject']) ? trim($numArr['subject']) : '';
                if ($num !== '' && (empty($cameraIds[$index]) || $cameraIds[$index] == null)) {
                    return redirect()->back()->withInput()->withErrors(['camera_id.' . $index => 'Camera Model is required when # of Camera is provided.']);
                }
            }

            foreach ($validatedData['camera_id'] as $index => $cameraId) {
                $cameraCommunityType = new CameraCommunityType(); 
                $cameraCommunityType->camera_id = $cameraId;
                $cameraCommunityType->camera_community_id = $cameraCommunity->id;
                $cameraCommunityType->number = $validatedData['addMoreInputFieldsCameraNumber'][$index]['subject'];

                // optional fields: SD card number, camera base number, internet cable length (meters)
                $cameraCommunityType->sd_card_number = isset($validatedData['addMoreInputFieldsSdCard'][$index]['subject']) ? $validatedData['addMoreInputFieldsSdCard'][$index]['subject'] : null;
                $cameraCommunityType->camera_base_number = isset($validatedData['addMoreInputFieldsCameraBaseNumber'][$index]['subject']) ? $validatedData['addMoreInputFieldsCameraBaseNumber'][$index]['subject'] : null;
                $cameraCommunityType->internet_cable_number = isset($validatedData['addMoreInputFieldsInternetCableNumber'][$index]['subject']) ? $validatedData['addMoreInputFieldsInternetCableNumber'][$index]['subject'] : null;

                $cameraCommunityType->save();
            }
        }

        if($request->nvr_id) {

            $validatedData = $request->validate([
                'nvr_id.*' => 'required',
                'addMoreInputFieldsNvrNumber.*.subject' => 'required',
                'addMoreInputFieldsNvrIpAddress.*.subject' => 'required',
            ]);

            foreach ($validatedData['nvr_id'] as $index => $cameraNvrId) {
                $nvrCommunityType = new NvrCommunityType(); 
                $nvrCommunityType->nvr_camera_id = $cameraNvrId;
                $nvrCommunityType->camera_community_id = $cameraCommunity->id;
                $nvrCommunityType->ip_address = $validatedData['addMoreInputFieldsNvrIpAddress'][$index]['subject'];
                $nvrCommunityType->number = $validatedData['addMoreInputFieldsNvrNumber'][$index]['subject'];
                $nvrCommunityType->save();
            }
        }

        if ($request->file('more_photos')) {

            foreach($request->more_photos as $photo) {

                $original_name = $photo->getClientOriginalName();
                $extra_name  = uniqid().'_'.time().'_'.uniqid().'.'.$photo->extension();
                $encoded_base64_image = substr($photo, strpos($photo, ',') + 1); 
                $destinationPath = public_path().'/cameras/community/' ;
                $photo->move($destinationPath, $extra_name);
    
                $cameraCommunityPhoto = new CameraCommunityPhoto();
                $cameraCommunityPhoto->slug = $extra_name;
                $cameraCommunityPhoto->camera_community_id = $id;
                $cameraCommunityPhoto->save();
            }
        }

        if ($request->file('new_photos')) {

            foreach($request->new_photos as $photo) {

                $original_name = $photo->getClientOriginalName();
                $extra_name  = uniqid().'_'.time().'_'.uniqid().'.'.$photo->extension();
                $encoded_base64_image = substr($photo, strpos($photo, ',') + 1); 
                $destinationPath = public_path().'/cameras/community/' ;
                $photo->move($destinationPath, $extra_name);
    
                $cameraCommunityPhoto = new CameraCommunityPhoto();
                $cameraCommunityPhoto->slug = $extra_name;
                $cameraCommunityPhoto->camera_community_id = $id;
                $cameraCommunityPhoto->save();
            }
        }

        // update new/more donors
        if($request->donors) {

            for($i=0; $i < count($request->donors); $i++) {

                $cameraDonor = new CameraCommunityDonor();
                $cameraDonor->donor_id = $request->donors[$i];
                $cameraDonor->camera_community_id = $id;
                $cameraDonor->save();
            }
        }

        if($request->new_donors) {

            for($i=0; $i < count($request->new_donors); $i++) {

                $cameraDonor = new CameraCommunityDonor();
                $cameraDonor->donor_id = $request->new_donors[$i];
                $cameraDonor->camera_community_id = $id;
                $cameraDonor->save();
            }
        }
        

        return redirect(URL::route('camera.all') . '#installed')->with('message', 'Installed Camera Updated Successfully!');
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */
    public function updateIpAddress(Request $request)
    {
        $id = $request->input('id');
        $ip_address = $request->input('ip_address');

        $nvrCommunityType = NvrCommunityType::findOrFail($id);
        $nvrCommunityType->ip_address = $ip_address;
        $nvrCommunityType->save();

        return response()->json(['success' => true]);
    }
    
    //Update Camera Components
    public function updateCommunityCamera(Request $request)
    {
        $id = $request->input('id');

        $cameraType = CameraCommunityType::find($id);

        if(!$cameraType) {
            return response()->json(['success' => 0, 'msg' => 'Invalid ID.']);
        }

        if($request->has('number')) $cameraType->number = $request->input('number');
        if($request->has('sd_card_number')) $cameraType->sd_card_number = $request->input('sd_card_number');
        if($request->has('camera_base_number')) $cameraType->camera_base_number = $request->input('camera_base_number');
        if($request->has('internet_cable_number')) $cameraType->internet_cable_number = $request->input('internet_cable_number');

        $cameraType->save();

        return response()->json(['success' => 1, 'msg' => 'Camera updated successfully']);
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCameraCommunity(Request $request)
    {
        $id = $request->id;

        $cameraCommunity = CameraCommunity::find($id);

        if($cameraCommunity) {

            $cameraCommunity->is_archived = 1;
            $cameraCommunity->save();

            $response['success'] = 1;
            $response['msg'] = 'Installed Camera in community Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCameraDonor(Request $request)
    {
        $id = $request->id;

        $cameraCommunityDonor = CameraCommunityDonor::find($id);

        if($cameraCommunityDonor) {

            $cameraCommunityDonor->is_archived = 1;
            $cameraCommunityDonor->save();

            $response['success'] = 1;
            $response['msg'] = 'Donor Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCommunityCamera(Request $request)
    {
        $id = $request->id;

        $cameraCommunityType = CameraCommunityType::find($id);

        if($cameraCommunityType) {

            $cameraCommunityType->delete();

            $response['success'] = 1;
            $response['msg'] = 'Installed Camera in community Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCommunityNvrCamera(Request $request)
    {
        $id = $request->id;

        $cameraNvrCommunityType = NvrCommunityType::find($id);

        if($cameraNvrCommunityType) {

            $cameraNvrCommunityType->delete();

            $response['success'] = 1;
            $response['msg'] = 'Installed NVR in community Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteCommunityCameraPhoto(Request $request)
    {
        $id = $request->id;

        $cameraCommunityPhoto = CameraCommunityPhoto::find($id);

        if($cameraCommunityPhoto) {

            $cameraCommunityPhoto->delete();

            $response['success'] = 1;
            $response['msg'] = 'Photo Deleted successfully'; 
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

        // If front-end requested default download (no type selected), return CameraCommunityExport5
        if ($request->has('download_default') && $request->download_default) {
            return Excel::download(new \App\Exports\CameraCommunityExport5($request), 'All Cameras Report.xlsx');
        }

        return Excel::download(new CameraCommunityExport($request), 'installed_cameras.xlsx'); 
    }
}
