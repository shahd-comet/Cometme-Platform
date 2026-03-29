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
use App\Models\CommunityStatus;
use App\Models\Compound;
use App\Models\CompoundHousehold;
use App\Models\CommunityWaterSource;
use App\Models\NearbyTown;
use App\Models\NearbySettlement;
use App\Models\CommunityProduct;
use App\Models\Donor;
use App\Models\EnergySystem;
use App\Models\EnergySystemType;
use App\Models\Household;
use App\Models\Settlement;
use App\Models\ServiceType;
use App\Models\PublicStructure;
use App\Models\PublicStructureCategory;
use App\Models\EnergySystemCycle;
use App\Models\ProductType;
use App\Models\Region;
use App\Models\H2oUser;
use App\Models\GridUser;
use App\Models\InternetUser;
use App\Models\Photo;
use App\Models\SubRegion;
use App\Models\WaterSource;
use App\Models\Town;
use App\Exports\CompoundHouseholdExport; 
use Carbon\Carbon;
use Image;
use DataTables;
use Excel;
 
class CommunityCompoundController extends Controller
{ 

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $empty = "";
        //  $updateButton = "<a type='button' class='updateCompoundCommunityHousehold' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#updateCompoundCommunityModal' ><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteCompoundHousehold' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";

        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 || 
            Auth::guard('user')->user()->user_type_id == 3) 
        {
                
            return $deleteButton;
        } else return $empty; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        $data1 = DB::table('compound_households')
            ->select(
                'compound_households.compound_id AS id',
                DB::raw('COUNT(compound_households.household_id) as total_household'),
                )
            ->groupBy('compound_households.compound_id')
            ->get();

        foreach($data1 as $d) {

            $compound = Compound::findOrFail($d->id);
            $compound->number_of_household = $d->total_household;
            $compound->save();
        }

      
        $householdCounts = DB::table('compound_households')
            ->join('households', 'households.id', 'compound_households.household_id')
            ->select(
                'compound_households.compound_id AS id',
                DB::raw(
                    'SUM(CASE WHEN households.is_archived = 0 THEN households.number_of_adults + households.number_of_children 
                        ELSE 0 END) as total_people'),
                DB::raw(
                    'SUM(CASE WHEN households.is_archived = 0 THEN households.number_of_male + households.number_of_female 
                        ELSE 0 END) as total_people1')
                )
            ->groupBy('compound_households.compound_id')
            ->get();

        foreach($householdCounts as $householdCounts) {

            $compound = Compound::findOrFail($householdCounts->id);
            if($householdCounts->total_people > $householdCounts->total_people1) $compound->number_of_people = $householdCounts->total_people;
            else $compound->number_of_people = $householdCounts->total_people1;
            $compound->save();
        }

        if (Auth::guard('user')->user() != null) {

            $communityFilter = $request->input('community_filter');
            $regionFilter = $request->input('region_filter');
            $subRegionFilter = $request->input('sub_region_filter');
            
            if ($request->ajax()) {  

                $query = DB::table('compound_households')
                    ->join('communities', 'compound_households.community_id', 
                        'communities.id')
                    ->join('regions', 'communities.region_id', 'regions.id')
                    ->join('sub_regions', 'communities.sub_region_id', 'sub_regions.id')
                    ->join('households', 'compound_households.household_id', 'households.id')
                    ->join('compounds', 'compound_households.compound_id', 'compounds.id') 
                    ->where('compound_households.is_archived', 0)
                    ->where('households.is_archived', 0)
                    ->select(
                        'compounds.english_name as english_name', 
                        'compounds.arabic_name as arabic_name',
                        'communities.english_name as community_english_name', 
                        'communities.arabic_name as community_arabic_name',
                        'compound_households.id as id', 
                        'regions.english_name as name',
                        'households.english_name as household',
                        DB::raw("'action' AS action")
                    );

                if ($request->search) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('communities.english_name', 'LIKE', "%$search%")
                            ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('regions.english_name', 'LIKE', "%$search%")
                            ->orWhere('regions.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('compounds.english_name', 'LIKE', "%$search%")
                            ->orWhere('compounds.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('households.english_name', 'LIKE', "%$search%")
                            ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                        ;
                    });
                }

                if ($regionFilter) $query->where('communities.region_id', $regionFilter);
                if ($subRegionFilter) $query->where('sub_regions.id', $subRegionFilter);
                if ($communityFilter) $query->where('communities.id', $communityFilter);

                $totalFiltered = $query->count();

                $columnIndex = $request->order[0]['column'] ?? 0;
                $columnName = $request->columns[$columnIndex]['data'] ?? 'compound_households.id';
                $direction  = $request->order[0]['dir'] ?? 'desc';

                $query->orderBy($columnName, $direction);

                $data = $query
                    ->offset($request->start)
                    ->limit($request->length)
                    ->get();

                $totalRecords = $data->count();
                
                foreach ($data as $row) {

                    $row->action = $this->generateActionButtons($row); 
                }

                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $totalFiltered,
                    'data' => $data
                ]);
            }  
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCompoundHousehold(Request $request)
    {
        $id = $request->id;

        $compoundCommunity = CompoundHousehold::findOrFail($id);
        $compoundCommunity->is_archived = 1;
        $compoundCommunity->save();

        $response['success'] = 1;
        $response['msg'] = 'Compound Household Deleted successfully'; 
        
        return response()->json($response); 
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request) 
    {
                
        return Excel::download(new CompoundHouseholdExport($request), 
            'Compound Households.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->household_id) {

            for($i=0; $i < count($request->household_id); $i++) {

                $compoundHousehold = new CompoundHousehold();
                $compoundHousehold->household_id = $request->household_id[$i];
                $compoundHousehold->compound_id = $request->compound_id;
                $compoundHousehold->community_id = $request->community_id;
                $compoundHousehold->energy_system_type_id = $request->energy_system_type_id;
                $compoundHousehold->save();

                $household = Household::findOrFail($request->household_id[$i]);
                if($request->energy_system_type_id) {
                    
                    $household->energy_system_type_id = $request->energy_system_type_id;
                    $household->save();
                }
            } 
       
            return redirect()->back()->with('message', 'New Compound Households Added Successfully!');
        } else {

            return redirect()->back()->with('error', 'You missed up selecting households!');
        }
            
    }

    /**
     * Get sub communities by community_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getByCommunity(Request $request) 
    {
        if (!$request->community_id) {

            $htmlHouseholds = '<option value="">Choose One...</option>';
            $htmlCompounds = '<option value="">Choose One...</option>';
            $htmlEnergySystems = '<option value="">Choose One...</option>';
        } else { 

            $htmlCompounds = '<option value="">Choose One...</option>';
            $htmlHouseholds = '<option value="">Choose One...</option>';
            $htmlEnergySystems = '<option value="">Choose One...</option>';
            
            $households = Household::where('community_id', $request->community_id)
                ->where('is_archived', 0)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('compound_households')
                        ->whereColumn('compound_households.household_id', 'households.id');
                })
                ->orderBy('english_name', 'ASC')
                ->get();


            $compounds = Compound::where('community_id', $request->community_id)
                ->where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();

            foreach ($compounds as $compound) {
                $htmlCompounds .= '<option value="'.$compound->id.'">'.$compound->english_name.'</option>';
            }

            foreach ($households as $household) {
                $htmlHouseholds .= '<option value="'.$household->id.'">'.$household->english_name.'</option>';
            }

            $energySystems = EnergySystem::where('community_id', $request->community_id)
                ->get();

            foreach($energySystems as $energySystem) {

                $htmlEnergySystems .= '<option value="'.$energySystem->id.'">'.$energySystem->name.'</option>';
            }
        }
 
        return response()->json([
            'htmlHouseholds' => $htmlHouseholds,
            'htmlCompounds' => $htmlCompounds,
            'htmlEnergySystems' => $htmlEnergySystems
        ]);
    }

    
    /**
     * Get sub communities by community_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getHouseholdByCompound(Request $request) 
    {
        if (!$request->compound_id) {

            $htmlHouseholds = '<option value="">Choose One...</option>';
        } else { 

            $htmlHouseholds = '<option value="">Choose One...</option>';
            
            $households =  DB::table('compound_households')
                ->join('compounds', 'compound_households.compound_id', 'compounds.id')
                ->join('households', 'compound_households.household_id', 'households.id')
                ->where('compound_households.is_archived', 0)
                ->where('compound_households.compound_id', $request->compound_id)
                ->select('households.id', 'households.english_name')
                ->get();

            foreach ($households as $household) {
                $htmlHouseholds .= '<option value="'.$household->id.'">'.$household->english_name.'</option>';
            }
        }

        return response()->json([
            'htmlHouseholds' => $htmlHouseholds
        ]);
    }

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function editPage($id)
    {
       
    } 

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        
    }
}