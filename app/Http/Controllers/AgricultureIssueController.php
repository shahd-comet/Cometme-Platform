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
use App\Exports\AgricultureIssuesExport;
use Auth;
use DB;
use Route;
use DataTables;
use Excel; 

class AgricultureIssueController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $updateButton = "<a type='button' class='updateAgricultureIssue' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteAgricultureIssue' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";

        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ) 
        {
                
            return $updateButton." ".$deleteButton;
        } else return "";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        // $incrementalNumber = 1; 
        // $agricultureIssues = AgricultureIssue::all();
        // foreach($agricultureIssues as $agricultureIssue) {

        //     $energyAction = AgricultureAction::findOrFail($agricultureIssue->agriculture_action_id);
        //     $actionCategory = ActionCategory::findOrFail($energyAction->action_category_id);
        //     $agricultureIssue->unique_id = $incrementalNumber;
            
        //     $agricultureIssue->comet_id = $actionCategory->comet_id . "E" . $energyAction->comet_id . $incrementalNumber;
        //     $agricultureIssue->save();

        //     $incrementalNumber++;
        // }

        if (Auth::guard('user')->user() != null) {

            $categoryFilter = $request->input('category_filter');
            $actionFilter = $request->input('action_filter');
  
            if ($request->ajax()) {

                $query = DB::table('agriculture_issues')
                    ->join('agriculture_actions', 'agriculture_issues.agriculture_action_id', 'agriculture_actions.id')
                    ->join('action_categories', 'agriculture_actions.action_category_id', 
                        'action_categories.id')
                    ->where('agriculture_issues.is_archived', 0) 
                    ->where('agriculture_actions.is_archived', 0)
                    ->select(
                        'agriculture_issues.id as id', 
                        'agriculture_issues.english_name', 
                        'agriculture_issues.arabic_name',
                        'agriculture_actions.english_name as agriculture_action', 
                        'action_categories.english_name as category',
                        'agriculture_issues.created_at as created_at',
                        'agriculture_issues.updated_at as updated_at'
                    );

                if ($request->search) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->orWhere('agriculture_issues.english_name', 'LIKE', "%$search%")
                            ->orWhere('agriculture_issues.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('agriculture_actions.english_name', 'LIKE', "%$search%")
                            ->orWhere('agriculture_actions.arabic_name', 'LIKE', "%$search%")
                            ->orWhere('action_categories.english_name', 'LIKE', "%$search%")
                            ->orWhere('action_categories.arabic_name', 'LIKE', "%$search%");
                    });
                }

                if($categoryFilter != null) $query->where('action_categories.id', $categoryFilter);
                if($actionFilter != null) $query->where('agriculture_actions.id', $actionFilter);
                
                $totalFiltered = $query->count();

                $columnIndex = $request->order[0]['column'] ?? 0;
                $columnName = $request->columns[$columnIndex]['data'] ?? 'agriculture_issues.id';
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
        $agricultureIssue = new AgricultureIssue();

        // Get last comet_id
        $last_comet_id = AgricultureIssue::latest('id')->value('unique_id') + 1;
        $agricultureIssue->english_name = $request->english_name;
        $agricultureIssue->arabic_name = $request->arabic_name;
        $agricultureIssue->agriculture_action_id = $request->agriculture_action_id;
        $agricultureIssue->comet_id = $last_comet_id;
        $agricultureAction = AgricultureAction::findOrFail($request->agriculture_action_id);
        $actionCategory = ActionCategory::findOrFail($agricultureAction->action_category_id);
        $agricultureIssue->unique_id = $last_comet_id;
        $agricultureIssue->comet_id = $actionCategory->comet_id . "AG" . $agricultureAction->comet_id . $last_comet_id;
        $agricultureIssue->notes = $request->notes;
        $agricultureIssue->save();
  
        return redirect()->back()
            ->with('message', 'New Issue Added Successfully!');
    }

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) 
    { 
        $agricultureIssue = AgricultureIssue::findOrFail($id);
        $agricultureActions = AgricultureAction::where("is_archived", 0)->get();
        $agricultureCategories = ActionCategory::where("is_archived", 0)->get();

        //die($agricultureIssue);

        return view('agriculture.issue.edit-issue', compact('agricultureIssue', 'agricultureCategories', 
            'agricultureActions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {     
       // dd($request->all());
        $agricultureIssue = AgricultureIssue::findOrFail($id);

        $last_comet_id = AgricultureIssue::latest('id')->value('unique_id') + 1;

        if($request->english_name) $agricultureIssue->english_name = $request->english_name;
        if($request->arabic_name) $agricultureIssue->arabic_name = $request->arabic_name;
        if($request->agriculture_action_id) {

            $agricultureIssue->agriculture_action_id = $request->agriculture_action_id;
            $agricultureAction = AgricultureAction::findOrFail($request->agriculture_action_id);

            $actionCategory = ActionCategory::findOrFail($agricultureAction->action_category_id);
            $agricultureIssue->unique_id = $last_comet_id;
            $agricultureIssue->comet_id = $actionCategory->comet_id . "AG" . $agricultureAction->comet_id . $last_comet_id;
        }
        if($request->notes == null) $agricultureIssue->notes = null;
        if($request->notes) $agricultureIssue->notes = $request->notes;
        $agricultureIssue->save(); 
  
        return redirect('/agriculture-maintenance')->with('message', 'Agriculture Issue Updated Successfully!');
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAgricultureIssue(Request $request)
    {
        $id = $request->id;

        $agricultureIssue = AgricultureIssue::find($id);

        if($agricultureIssue) {

            $agricultureIssue->is_archived = 1;
            $agricultureIssue->save();

            $response['success'] = 1;
            $response['msg'] = 'Agriculture Issue Deleted successfully'; 
        } else {

            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response); 
    }

    /**
     * Get a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getActionBasedOnCategory(Request $request)
    {
        if (!$request->id) {

            $html = '<option disabled selected>Choose One...</option>';
        } else {

            $html = '<option  disabled selected>Choose One...</option>';

            $agricultureActions = AgricultureAction::where('action_category_id', $request->id)
                ->orderBy('english_name', 'ASC')
                ->where('is_archived', 0)
                ->get();

            foreach ($agricultureActions as $agricultureAction) {

                $html .= '<option value="'.$agricultureAction->id.'">'.$agricultureAction->english_name.'</option>';
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
                
        return Excel::download(new AgricultureIssuesExport($request), 'agriculture_issues_actions.xlsx');
    }
}