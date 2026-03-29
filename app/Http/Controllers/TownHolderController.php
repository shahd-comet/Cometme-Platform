<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TownHolder;
use App\Models\Town;
use Auth;
use DB;

class TownHolderController extends Controller
{
    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $updateButton = "<a type='button' class='updateTownHolder' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#updateWaterUserModal' ><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteTownHolder' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        $viewButton = "<a type='button' class='viewTownHolder' data-bs-toggle='modal' data-bs-target='#townHolderDetails' data-id='".$row->id."'><i class='fa-solid fa-eye text-primary'></i></a>";

        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ) 
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
        if (!Auth::guard('user')->user()) {

            return view('errors.not-found');
        }

        $townFilter = $request->input('town_filter');

        if ($request->ajax()) {

            $query = DB::table('town_holders') 
                ->join('towns', 'town_holders.town_id', 'towns.id')
                ->where('town_holders.is_archived', 0)
                ->select(
                    'town_holders.id',
                    'town_holders.english_name',
                    'town_holders.arabic_name',
                    'towns.english_name as town',
                    'town_holders.phone_number',
                    'town_holders.created_at',
                    DB::raw("CASE WHEN town_holders.has_internet = 1 THEN 'Yes' ELSE 'No' END as has_internet"),
                    DB::raw("CASE WHEN town_holders.has_refrigerator = 1 THEN 'Yes' ELSE 'No' END as has_refrigerator")
                );

            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('towns.english_name', 'LIKE', "%{$search}%")
                      ->orWhere('town_holders.english_name', 'LIKE', "%{$search}%")
                      ->orWhere('town_holders.arabic_name', 'LIKE', "%{$search}%")
                      ->orWhere('towns.arabic_name', 'LIKE', "%{$search}%")
                      ->orWhere('town_holders.phone_number', 'LIKE', "%{$search}%")
                      ;
                });
            }

            if ($townFilter) $query->where('towns.id', $townFilter);

            $totalFiltered = $query->count();

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'town_holders.id';
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get Last comet_id
        $last_comet_id = TownHolder::latest('id')->value('comet_id');

        $townHolder = TownHolder::create($request->all());
        $townHolder->comet_id = ++$last_comet_id;
        $townHolder->english_name = $request->english_name;
        $townHolder->town_id = $request->town_id;
        if($request->has_internet) $townHolder->has_internet = $request->has_internet;
        if($request->has_refrigerator) $townHolder->has_refrigerator = $request->has_refrigerator;
        $townHolder->save();

        return redirect()->back()->with('message', 'New Town Holder Added Successfully!');
    }

     /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        $townHolder = TownHolder::findOrFail($id);
        $towns = Town::where('is_archived', 0)->get();

        return view('holders.town.edit', compact('townHolder', 'towns'));
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */ 
    public function update(Request $request, $id)
    {
        $townHolder = TownHolder::findOrFail($id);

        if($request->english_name) $townHolder->english_name = $request->english_name;
        if($request->arabic_name) $townHolder->arabic_name = $request->arabic_name;
        if($request->town_id) $townHolder->town_id = $request->town_id;
        if($request->phone_number) $townHolder->phone_number = $request->phone_number;
        $townHolder->has_internet = $request->has_internet;
        $townHolder->has_refrigerator = $request->has_refrigerator;
        $townHolder->save();

        return redirect('/other-holder')->with('message', 'Town Holder Updated Successfully!');
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $townHolder = TownHolder::findOrFail($id);
        $town = Town::where('id', $townHolder->town_id)->first();

        $response['town'] = $town;
        $response['townHolder'] = $townHolder;

        return response()->json($response);
    }

    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteTownHolder($id)
    {
        $townHolder = TownHolder::find($id);
        
        if ($townHolder) {

            $townHolder->is_archived = 1; 
            $townHolder->save(); 
            
            return response()->json(['status' => 'success', 'message' => 'Record deleted successfully.']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Record not found.']);
    }
}
