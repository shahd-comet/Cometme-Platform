<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Models\ActionCategory;
use App\Models\AgricultureIssue;
use App\Models\AgricultureAction;
use App\Exports\EnergyActionExport;

use Auth;
use DB;
use Route;
use DataTables;
use Excel;

class AgricultureActionController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $updateButton = "<a type='button' class='updateAgricultureAction' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteAgricultrueAction' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";

        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2) 
        {
                
            return $updateButton. " ". $deleteButton ;
        } else return "";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (Auth::guard('user')->user() != null) {

            $categoryFilter = $request->input('category_filter');
            $actionFilter = $request->input('action_filter');

            if ($request->ajax()) {

                $query = DB::table('agriculture_actions')
                    ->join('action_categories', 'agriculture_actions.action_category_id', 
                        'action_categories.id')
                    ->where('agriculture_actions.is_archived', 0)
                    ->select('agriculture_actions.id as id', 
                        'agriculture_actions.english_name', 
                        'agriculture_actions.arabic_name',
                        'action_categories.english_name as category',
                        'agriculture_actions.created_at as created_at',
                        'agriculture_actions.updated_at as updated_at'
                    );

                if ($request->search) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->orWhere('agriculture_actions.english_name', 'LIKE', "%$search%")
                            ->orWhere('agriculture_actions.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('action_categories.english_name', 'LIKE', "%$search%")
                            ->orWhere('action_categories.arabic_name', 'LIKE', "%$search%");
                    });
                }

                if($categoryFilter != null) $query->where('action_categories.id', $categoryFilter);
                if($actionFilter != null) $query->where('agriculture_actions.id', $actionFilter);
          
                $totalFiltered = $query->count();

                $columnIndex = $request->order[0]['column'] ?? 0;
                $columnName = $request->columns[$columnIndex]['data'] ?? 'agriculture_actions.id';
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {     
        $agricultureAction = new AgricultureAction();
        // Get last comet_id
        $last_comet_id = AgricultureAction::latest('id')->value('comet_id') + 1;
        $agricultureAction->english_name = $request->english_name;
        $agricultureAction->arabic_name = $request->arabic_name;
        $agricultureAction->action_category_id = $request->action_category_id;
        $agricultureAction->comet_id = $last_comet_id;
        $agricultureAction->notes = $request->notes;
        $agricultureAction->save();
  
        return redirect()->back()
            ->with('message', 'New Action Added Successfully!');
    }

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    {
        $agricultureAction = AgricultureAction::findOrFail($id);
        $energyCategories = ActionCategory::where("is_archived", 0)->get();

        return view('agriculture.issue.edit-action', compact('energyCategories', 'agricultureAction'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {     
        $agricultureAction = AgricultureAction::findOrFail($id);

        if($request->english_name) $agricultureAction->english_name = $request->english_name;
        if($request->arabic_name) $agricultureAction->arabic_name = $request->arabic_name;
        if($request->action_category_id) $agricultureAction->action_category_id = $request->action_category_id;
        if($request->notes) $agricultureAction->notes = $request->notes;
        $agricultureAction->save();
  
        return redirect('/agriculture-maintenance')->with('message', 'Agriculture Action Updated Successfully!');
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAgricultrueAction(Request $request)
    {
        $id = $request->id;

        $agricultureAction = AgricultureAction::find($id);

        if($agricultureAction) {

            $agricultureAction->is_archived = 1;
            $agricultureAction->save(); 

            $response['success'] = 1;
            $response['msg'] = 'Agriculture Action Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }
}
