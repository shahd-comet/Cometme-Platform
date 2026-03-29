<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB;
use Route;
use App\Models\User;
use App\Models\Community;
use App\Models\EnergyUser;
use App\Models\Household;
use App\Models\PublicStructure;
use App\Models\PublicStructureCategory;
use App\Models\RefrigeratorHolder;
use App\Models\RefrigeratorHolderReceiveNumber;
use App\Exports\RefrigeratorExport;
use App\Imports\ImportRefrigerator;
use Carbon\Carbon; 
use Image;
use DataTables;
use Excel;

class RefrigeratorHolderController extends Controller
{
    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $viewButton = "<a type='button' class='viewRefrigeratorHolder' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#viewRefrigeratorHolderModal'><i class='fa-solid fa-eye text-info'></i></a>";
        $updateButton = "<a type='button' class='updateRefrigeratorHolder' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteRefrigeratorHolder' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        
        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ||
            Auth::guard('user')->user()->user_type_id == 3 ||
            Auth::guard('user')->user()->user_type_id == 7) 
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

        // $numbers = RefrigeratorHolderReceiveNumber::get();
        // foreach($numbers as $number) {

        //     $holder = RefrigeratorHolder::where("community_name", $number->community_name)
        //         ->where("household_name", $number->household_name)
        //         ->where("year", $number->year)
        //         ->get();

        //     if($holder) {

        //         $number->refrigerator_holder_id = $holder[0]->id;
        //         $number->save();
        //     }

        //     $holder1 = RefrigeratorHolder::where("community_name", $number->community_name)
        //         ->where("household_name", $number->household_name)
        //         ->where("maintenance_year", $number->maintenance_year)
        //         ->get();

        //     if($holder1) {
                
        //         $number->refrigerator_holder_id = $holder1[0]->id;
        //         $number->save();
        //     }
            
        // }


        // $holders = RefrigeratorHolder::where("community_id", 0)->get();
        
        // foreach($holders as $holder) {
        //     $community = Community::where('english_name', $holder->community_name)->first();
      
        //     $holder->community_id = $community->id;
        //     $holder->save();
        // }

        $communityFilter = $request->input('community_filter');
        $typeFilter = $request->input('type_filter');
        $dateFilter = $request->input('date_filter');
        $yearFilter = $request->input('year_filter');
        $meterFilter = $request->input('meter_filter');
        $regionFilter = $request->input('region_filter');
        $energyTypeFilter = $request->input('system_type_filter'); 
        $cycleFilter = $request->input('cycle_filter');

        if (Auth::guard('user')->user() != null) {

            if ($request->ajax()) {

                $data = DB::table('refrigerator_holders')
                    ->join('communities', 'refrigerator_holders.community_id', 'communities.id')
                    ->leftJoin('households', 'refrigerator_holders.household_id', 'households.id')
                    ->leftJoin('public_structures', 'refrigerator_holders.public_structure_id', 
                        'public_structures.id')
                    ->leftJoin('all_energy_meters as energy_users', 'energy_users.household_id', 'households.id')
                    ->leftJoin('all_energy_meters as energy_publics', 'energy_publics.public_structure_id', 'public_structures.id')
                    ->where('refrigerator_holders.is_archived', 0); 
                
                $data->when($regionFilter, fn($q) => $q->where('communities.region_id', $regionFilter))
                    ->when($communityFilter, fn($q) => $q->where('communities.id', $communityFilter))
                    ->when($typeFilter, function ($q) use ($typeFilter) {
                        $q->where(function ($sub) use ($typeFilter) {
                            $sub->where('energy_users.installation_type_id', $typeFilter)
                                ->orWhere('energy_publics.installation_type_id', $typeFilter);
                        });
                    })
                    ->when($energyTypeFilter, function ($q) use ($energyTypeFilter) {
                        $q->where(function ($sub) use ($energyTypeFilter) {
                            $sub->where('energy_users.energy_system_type_id', $energyTypeFilter)
                                ->orWhere('energy_publics.energy_system_type_id', $energyTypeFilter);
                        });
                    })
                    ->when($meterFilter, function ($q) use ($meterFilter) {
                        $q->where(function ($sub) use ($meterFilter) {
                            $sub->where('energy_users.meter_case_id', $meterFilter)
                                ->orWhere('energy_publics.meter_case_id', $meterFilter);
                        });
                    })
                    ->when($cycleFilter, function ($q) use ($cycleFilter) {
                        $q->where(function ($sub) use ($cycleFilter) {
                            $sub->where('energy_users.energy_system_cycle_id', $cycleFilter)
                                ->orWhere('energy_publics.energy_system_cycle_id', $cycleFilter);
                        });
                    })
                    ->when($dateFilter, fn($q) => $q->where('refrigerator_holders.date', '>=', $dateFilter));

                $search = $request->input('search.value'); 

                if (!empty($search)) {
                    $data->where(function ($w) use ($search) {
                        $w->where('communities.english_name', 'LIKE', "%$search%")
                            ->orWhere('households.english_name', 'LIKE', "%$search%")
                            ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('households.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('public_structures.english_name', 'LIKE', "%$search%")
                            ->orWhere('public_structures.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('energy_users.meter_number', 'LIKE', "%$search%")
                            ->orWhere('energy_publics.meter_number', 'LIKE', "%$search%");
                    });
                }

                $totalRecords = DB::table('refrigerator_holders')
                    ->where('refrigerator_holders.is_archived', 0)
                    ->count();

                $filteredRecords = (clone $data)->count();

                $data = $data->select(
                    'refrigerator_holders.refrigerator_type_id', 'refrigerator_holders.date',
                    'refrigerator_holders.id as id', 'refrigerator_holders.created_at as created_at', 
                    'refrigerator_holders.updated_at as updated_at', 
                    'communities.english_name as community_name',
                    DB::raw('IFNULL(households.english_name, public_structures.english_name) 
                        as holder'),
                    DB::raw('IFNULL(energy_users.meter_number, energy_publics.meter_number) 
                        as meter_number'),
                    'refrigerator_holders.payment', 'refrigerator_holders.is_paid', 
                    'refrigerator_holders.status', 
                    'refrigerator_holders.year',
                    DB::raw("'action' AS action")
                )
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'community_id' => 'required',
        ]); 

        $refrigeratorHolder = new RefrigeratorHolder(); 
        if($request->is_household == "no") {

            $refrigeratorHolder->public_structure_id = $request->public_structure_id;
        } else {

            $refrigeratorHolder->household_id = $request->household_id;

            if($request->phone_number) {

                $household = Household::findOrFail($request->household_id);
                $household->phone_number = $request->phone_number;
                $household->save();
            }
        }

        $refrigeratorHolder->refrigerator_type_id = $request->refrigerator_type_id;
        $refrigeratorHolder->community_id = $request->community_id;
        $refrigeratorHolder->number_of_fridge = $request->number_of_fridge;
        $refrigeratorHolder->date = $request->date;
        $refrigeratorHolder->year = $request->year;
        $refrigeratorHolder->is_paid = $request->is_paid;
        $refrigeratorHolder->payment = $request->payment;
        $refrigeratorHolder->notes = $request->notes;
        $refrigeratorHolder->save();
        $id = $refrigeratorHolder->id;

        if($request->receive_number) {

            $newRefrigeratorHolderNumber = new RefrigeratorHolderReceiveNumber();
            $newRefrigeratorHolderNumber->receive_number = $request->receive_number;
            $newRefrigeratorHolderNumber->refrigerator_holder_id = $id;
            $newRefrigeratorHolderNumber->year = $refrigeratorHolder->year;
            $newRefrigeratorHolderNumber->maintenance_year = $refrigeratorHolder->maintenance_year;
            $newRefrigeratorHolderNumber->save();
        }
        
        return redirect()->back()->with('message', 'New Refrigerator Holder Added Successfully!');
    }

    /**
     * Get households by community_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPhoneNumber(Request $request)
    {
        $household = Household::where('id', $request->household_id)->get();

        return response()->json(['household' => $household]);
    }

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        $refrigeratorHolder = RefrigeratorHolder::findOrFail($id);
        $refrigeratorUsers = [];

        if($refrigeratorHolder->household_id) $refrigeratorUsers = Household::where('is_archived', 0)
            ->where('community_id', $refrigeratorHolder->community_id)
            ->get();

        if($refrigeratorHolder->public_structure_id) $refrigeratorUsers = PublicStructure::where('is_archived', 0)
            ->where('community_id', $refrigeratorHolder->community_id)
            ->where('comet_meter', 0)
            ->get();

        $refrigeratorHolderNumber = RefrigeratorHolderReceiveNumber::where("refrigerator_holder_id", 
            $id)->get(); 

        return view('users.refrigerator.edit', compact('refrigeratorHolder', 'refrigeratorHolderNumber', 'refrigeratorUsers'));
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $refrigeratorHolder = RefrigeratorHolder::findOrFail($id);
        $refrigeratorHolderNumber = RefrigeratorHolderReceiveNumber::where("refrigerator_holder_id", 
            $id)->get();

        if($request->household_id) $refrigeratorHolder->household_id = $request->household_id;
        if($request->public_structure_id) $refrigeratorHolder->public_structure_id = $request->public_structure_id;
        $refrigeratorHolder->refrigerator_type_id = $request->refrigerator_type_id;
        $refrigeratorHolder->number_of_fridge = $request->number_of_fridge;
        $refrigeratorHolder->date = $request->date;
        $refrigeratorHolder->year = $request->year;
        $refrigeratorHolder->is_paid = $request->is_paid;
        $refrigeratorHolder->payment = $request->payment;

        if($request->receive_number) {

            if(count($refrigeratorHolderNumber) > 0) {

                $refrigeratorHolderNumber[0]->receive_number = $request->receive_number;
                $refrigeratorHolderNumber[0]->save();
            } else {

                $newRefrigeratorHolderNumber = new RefrigeratorHolderReceiveNumber();
                $newRefrigeratorHolderNumber->receive_number = $request->receive_number;
                $newRefrigeratorHolderNumber->refrigerator_holder_id = $id;
                $newRefrigeratorHolderNumber->year = $refrigeratorHolder->year;
                $newRefrigeratorHolderNumber->maintenance_year = $refrigeratorHolder->maintenance_year;
                $newRefrigeratorHolderNumber->save();
            } 
        }

        if($refrigeratorHolder->household_id) {

            if($request->phone_number) {

                $household = Household::findOrFail($refrigeratorHolder->household_id);
                $household->phone_number = $request->phone_number;
                $household->save();
            }
        }

        $refrigeratorHolder->notes = $request->notes;
        $refrigeratorHolder->save();

        return redirect('/all-meter')->with('message', 'Refrigerator Holder Updated Successfully!');
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $refrigeratorHolder = RefrigeratorHolder::findOrFail($id);
        $refrigeratorHolderNumber = RefrigeratorHolderReceiveNumber::where("refrigerator_holder_id", 
            $id)->get();
        $community = Community::where('id', $refrigeratorHolder->community_id)->first();
        $household = Household::where('id', $refrigeratorHolder->household_id)->first();
        $public = PublicStructure::where('id', $refrigeratorHolder->public_structure_id)->first();

        $response['refrigerator'] = $refrigeratorHolder;
        $response['community'] = $community;
        $response['household'] = $household;
        $response['public'] = $public;
        $response['refrigeratorHolderNumber'] = $refrigeratorHolderNumber;

        return response()->json($response);
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteRefrigeratorHolder(Request $request)
    {
        $id = $request->id;

        $refrigeratorHolder = RefrigeratorHolder::find($id);

        if($refrigeratorHolder) {

            $refrigeratorHolder->is_archived = 1;
            $refrigeratorHolder->save();

            $response['success'] = 1;
            $response['msg'] = 'Refrigerator Holder Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * Get resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getHouseholdByCommunity($community_id, $flag)
    {
        $html = "<option disabled selected>Choose one...</option>";

        if($flag == "user") {

            $households = DB::table('refrigerator_holders')
                ->join('households', 'refrigerator_holders.household_id', 'households.id')
                ->where("refrigerator_holders.community_id", $community_id)
                ->where('refrigerator_holders.is_archived', 0)
                ->orderBy('households.english_name', 'ASC')
                ->select('households.id', 'households.english_name')
                ->get();
        } else if($flag == "public") {
 
            $households = DB::table('refrigerator_holders')
                ->join('public_structures', 'refrigerator_holders.public_structure_id', 'public_structures.id')
                ->where('refrigerator_holders.community_id', $community_id)
                ->where('refrigerator_holders.is_archived', 0)
                ->orderBy('public_structures.english_name', 'ASC')
                ->select('public_structures.id as id', 'public_structures.english_name')
                ->get();
        } 

        foreach ($households as $household) {
            $html .= '<option value="'.$household->id.'">'.$household->english_name.'</option>';
        }
        
        return response()->json(['html' => $html]);
    }

    /**
     * Get resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPublicByCommunity($community_id)
    {
        $html = "<option disabled selected>Choose one...</option>";

        if (!$community_id) {

            $html = "<option disabled selected>Choose one...</option>";
        } else {

            $publics = DB::table('refrigerator_holders')
                ->join('public_structures', 'refrigerator_holders.public_structure_id', 
                    '=', 'public_structures.id')
                ->where("refrigerator_holders.community_id", $community_id)
                ->select('public_structures.id', 'public_structures.english_name')
                ->get();
                
            foreach ($publics as $public) {

                $html .= '<option value="'.$public->id.'">'.$public->english_name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function export(Request $request) 
    {
                
        return Excel::download(new RefrigeratorExport($request), 'rfrigerators.xlsx');
    }

    /**
     * 
     * @return \Illuminate\Support\Collection
     */
    public function import(Request $request)
    {
        Excel::import(new ImportRefrigerator, $request->file('file')); 
            
        return back()->with('success', 'Excel Data Imported successfully.');
    }
}