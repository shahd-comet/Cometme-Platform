<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TownHolder;
use App\Models\Community;
use Auth;
use DB;

class ActivistHolderController extends Controller
{
    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $updateButton = "<a type='button' class='updateActivistHolder' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#updateActivistModal' ><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteActivistHolder' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        $viewButton = "<a type='button' class='viewActivistHolder' data-bs-toggle='modal' data-bs-target='#activistHolderDetails' data-id='".$row->id."'><i class='fa-solid fa-eye text-primary'></i></a>";

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

        $communityFilter = $request->input('community_filter');

        if ($request->ajax()) {

            $query = DB::table('town_holders') 
                ->join('communities', 'town_holders.community_id', 'communities.id')
                ->where('town_holders.is_archived', 0)
                ->where('town_holders.is_activist', 1)
                ->select(
                    'town_holders.id',
                    'town_holders.english_name',
                    'town_holders.arabic_name',
                    'communities.english_name as community',
                    'town_holders.phone_number',
                    'town_holders.created_at',
                    DB::raw("CASE WHEN town_holders.has_internet = 1 THEN 'Yes' ELSE 'No' END as has_internet"),
                    DB::raw("CASE WHEN town_holders.has_refrigerator = 1 THEN 'Yes' ELSE 'No' END as has_refrigerator")
                );

            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('communities.english_name', 'LIKE', "%{$search}%")
                      ->orWhere('town_holders.english_name', 'LIKE', "%{$search}%")
                      ->orWhere('town_holders.arabic_name', 'LIKE', "%{$search}%")
                      ->orWhere('communities.arabic_name', 'LIKE', "%{$search}%")
                      ->orWhere('town_holders.phone_number', 'LIKE', "%{$search}%")
                      ;
                });
            }

            if ($communityFilter) $query->where('communities.id', $communityFilter);

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
        $townHolder->community_id = $request->community_id;
        $townHolder->is_activist = 1;
        if($request->has_internet) $townHolder->has_internet = $request->has_internet;
        if($request->has_refrigerator) $townHolder->has_refrigerator = $request->has_refrigerator;
        $townHolder->save();

        return redirect()->back()->with('message', 'New Activist Holder Added Successfully!');
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
        $communities = Community::where('is_archived', 0)->get();

        return view('holders.activist.edit', compact('townHolder', 'communities'));
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
        if($request->community_id) $townHolder->community_id = $request->community_id;
        if($request->phone_number) $townHolder->phone_number = $request->phone_number;
        $townHolder->has_internet = $request->has_internet;
        $townHolder->has_refrigerator = $request->has_refrigerator;
        $townHolder->save();

        return redirect('/other-holder')->with('message', 'Activist Updated Successfully!');
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
        $community = Community::where('id', $townHolder->community_id)->first();

        $response['community'] = $community;
        $response['townHolder'] = $townHolder;

        return response()->json($response);
    }
}
