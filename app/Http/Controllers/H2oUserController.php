<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class H2oUserController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $updateButton = "<a type='button' class='updateWaterUser' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#updateWaterUserModal' ><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteWaterUser' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        $viewButton = "<a type='button' class='viewWaterUser' data-id='".$row->id."' ><i class='fa-solid fa-eye text-info'></i></a>";

        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ||
            Auth::guard('user')->user()->user_type_id == 5 ||
            Auth::guard('user')->user()->user_type_id == 11) 
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

        $regionFilter = $request->input('region_filter');
        $communityFilter = $request->input('community_filter');
        $yearFilter = $request->input('year_filter');

        if ($request->ajax()) {
 
            $queryHousehold = DB::table('all_water_holders')
                ->join('communities', 'all_water_holders.community_id', 'communities.id')
                ->leftJoin('public_structures', 'all_water_holders.public_structure_id', 'public_structures.id')
                ->leftJoin('households', 'all_water_holders.household_id', 'households.id')
                ->leftJoin('water_holder_statuses', 'households.water_holder_status_id', 'water_holder_statuses.id')
                ->join('h2o_users', 'h2o_users.household_id', 'households.id')
                ->where('h2o_users.is_archived', 0)
                ->where('all_water_holders.is_archived', 0)
                ->where('all_water_holders.is_main', "Yes")
                ->select(
                    'all_water_holders.id',
                    'households.english_name as holder',
                    'communities.english_name as community_name',
                    'h2o_users.installation_year',
                    'h2o_users.number_of_h20',
                    'all_water_holders.created_at'
                );

            $queryPublic = DB::table('all_water_holders')
                ->join('communities', 'all_water_holders.community_id', 'communities.id')
                ->leftJoin('public_structures', 'all_water_holders.public_structure_id', 'public_structures.id')
                ->join('h2o_public_structures', 'h2o_public_structures.public_structure_id', 'public_structures.id')
                ->where('h2o_public_structures.is_archived', 0)
                ->where('all_water_holders.is_archived', 0)
                ->where('all_water_holders.is_main', "Yes")
                ->select(
                    'all_water_holders.id',
                    'public_structures.english_name as holder',
                    'communities.english_name as community_name',
                    'h2o_public_structures.installation_year',
                    'h2o_public_structures.number_of_h20',
                    'all_water_holders.created_at'
                );

            
            if ($request->search) {

                $search = $request->search;

                $queryHousehold->where(function ($q) use ($search) {
                    $q->where('households.english_name', 'LIKE', "%{$search}%")
                      ->orWhere('communities.english_name', 'LIKE', "%{$search}%")
                      ->orWhere('households.arabic_name', 'LIKE', "%{$search}%")
                      ->orWhere('communities.arabic_name', 'LIKE', "%{$search}%")
                      ->orWhere('h2o_users.installation_year', 'LIKE', "%{$search}%")
                      ->orWhere('h2o_users.number_of_h20', 'LIKE', "%{$search}%");
                });

                $queryPublic->where(function ($q) use ($search) {
                    $q->where('public_structures.english_name', 'LIKE', "%{$search}%")
                    ->orWhere('communities.english_name', 'LIKE', "%{$search}%")
                    ->orWhere('public_structures.arabic_name', 'LIKE', "%{$search}%")
                    ->orWhere('communities.arabic_name', 'LIKE', "%{$search}%")
                    ->orWhere('h2o_public_structures.installation_year', 'LIKE', "%{$search}%")
                    ->orWhere('h2o_public_structures.number_of_h20', 'LIKE', "%{$search}%");
                });
            }

            if ($regionFilter) {

                $queryHousehold->where('communities.region_id', $regionFilter);
                $queryPublic->where('communities.region_id', $regionFilter);
            }
            if ($communityFilter) {
                
                $queryHousehold->where('communities.id', $communityFilter);
                $queryPublic->where('communities.id', $communityFilter);
            }
            if ($yearFilter != null) {
                
                $queryHousehold->where('h2o_users.installation_year', $yearFilter);
                $queryPublic->where('h2o_public_structures.installation_year', $yearFilter);
            }
            
            $query = $queryHousehold->union($queryPublic);
            
            $totalFiltered = $query->count();

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'all_water_holders.id';
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
