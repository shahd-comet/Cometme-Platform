<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\VendorRegion;
use App\Models\Vendor;
use App\Models\VendorUserName;
use App\Models\VendorService;
use App\Models\CommunityVendor;
use App\Models\User;
use App\Models\Community;
use App\Models\ServiceType;
use App\Models\Region;
use App\Models\Town;
use App\Exports\VendingPointAndHistoryExport;
use Carbon\Carbon;
use Auth;
use DB;
use DataTables;
use Excel;
use Route;

class VendingPointController extends Controller
{

    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $detailsButton = "<a type='button' class='detailsVendorButton' data-bs-toggle='modal' data-bs-target='#vendorDetails' data-id='".$row->id."'><i class='fa-solid fa-eye text-primary'></i></a>";
        $updateButton = "<a type='button' class='updateVendor' data-id='".$row->id."'><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteVendor' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";

        if(Auth::guard('user')->user()->user_type_id != 1 || 
            Auth::guard('user')->user()->user_type_id != 2 || 
            Auth::guard('user')->user()->user_type_id != 4) 
        {

            return $detailsButton." ". $updateButton." ".$deleteButton;

        } else return $detailsButton; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
        if (Auth::guard('user')->user() != null) {

            $regionFilter = $request->input('region_filter');
            $communityFilter = $request->input('community_filter');
            $townFilter = $request->input('town_filter');
            $serviceFilter = $request->input('service_filter');

            if ($request->ajax()) {

                $servicesSub = DB::table('vendor_services')
                    ->join('service_types', 'vendor_services.service_type_id', '=', 'service_types.id')
                    ->select(
                        'vendor_services.vendor_id',
                        DB::raw('GROUP_CONCAT(distinct service_types.service_name) as services')
                    )
                    ->groupBy('vendor_services.vendor_id');


                $data = DB::table('vendors')
                    ->leftJoin('vendor_user_names', 'vendors.vendor_username_id', 'vendor_user_names.id')
                    ->leftJoin('vendor_regions', 'vendors.vendor_region_id', 'vendor_regions.id')
                    ->leftJoin('communities', 'vendors.community_id', 'communities.id')
                    ->leftJoin('towns', 'vendors.town_id', 'towns.id')
                    ->where('vendors.is_archived', 0);

                $data->when($regionFilter, fn($q) => $q->where('vendors.vendor_region_id', $regionFilter))
                    ->when($communityFilter, fn($q) => $q->where('vendors.community_id', $communityFilter))
                    ->when($townFilter, fn($q) => $q->where('vendors.town_id', $townFilter))
                    ->when($serviceFilter, function ($q) use ($serviceFilter) {
                        $q->whereExists(function ($sub) use ($serviceFilter) {
                            $sub->select(DB::raw(1))
                                ->from('vendor_services')
                                ->whereColumn('vendor_services.vendor_id', 'vendors.id')
                                ->where('vendor_services.service_type_id', $serviceFilter);
                        });
                    });

                if (!empty($search)) {
                    $data->where(function ($w) use ($search) {
                        $w->where('vendor_regions.english_name', 'LIKE', "%$search%")
                        ->orWhere('vendor_regions.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('vendors.english_name', 'LIKE', "%$search%")
                        ->orWhere('vendors.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('communities.english_name', 'LIKE', "%$search%")
                        ->orWhere('communities.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('towns.english_name', 'LIKE', "%$search%")
                        ->orWhere('towns.arabic_name', 'LIKE', "%$search%")
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->select(DB::raw(1))
                                ->from('vendor_services')
                                ->join('service_types', 'vendor_services.service_type_id', '=', 'service_types.id')
                                ->whereColumn('vendor_services.vendor_id', 'vendors.id')
                                ->where('service_types.service_name', 'LIKE', "%$search%");
                        });
                    });
                }

                $totalRecords = DB::table('vendors')
                    ->where('is_archived', 0)
                    ->count();

                $filteredRecords = (clone $data)
                    ->distinct('vendors.id')
                    ->count('vendors.id');

                $data = $data
                    ->leftJoinSub($servicesSub, 'svc', function ($join) {
                        $join->on('vendors.id', '=', 'svc.vendor_id');
                    })
                    ->select(
                        'vendors.id',
                        'vendors.english_name',
                        'vendors.arabic_name',
                        'vendors.phone_number', 
                        'vendors.status',
                        'vendor_regions.english_name as region',
                        'towns.english_name as town',
                        'svc.services',
                        DB::raw("'action' AS action")
                    )
                    ->distinct()
                    ->latest('vendors.id')
                    ->groupby('vendors.id')
                    ->skip($request->start)
                    ->take($request->length)
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
        } 
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $communities = Community::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->get();
        $regions = Region::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->get(); 
        $vendorRegions = VendorRegion::where('is_archived', 0)->get();
        $vendorUsers = VendorUserName::all();
        $towns = Town::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->get();
        $services = ServiceType::where('is_archived', 0)->where("service_name", "!=", "Camera")->get();

        return view('vendor.create', compact('communities', 'regions', 'vendorRegions', 'vendorUsers',
            'towns', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        //dd($request->all());

        $vendor = new Vendor();
        $vendor->english_name = $request->english_name;
        $vendor->arabic_name = $request->arabic_name;
        $vendor->vendor_region_id = $request->vendor_region_id;
        $vendor->phone_number = $request->phone_number;
        $vendor->additional_phone_number = $request->additional_phone_number;
        $vendor->status = "Active";
        $vendor->notes = $request->notes;
        if($request->community_town == "community") $vendor->community_id = $request->community_town_id;
        else if($request->community_town == "town") $vendor->town_id = $request->community_town_id;
        $vendor->save();


        // Handle Services + Vendor Usernames
        if ($request->has('service_type_id')) {

            foreach ($request->service_type_id as $serviceId) {

                // Get usernames for this service
                if (!isset($request->vendor_user_name_id[$serviceId])) {
                    continue;
                }
                $usernames = $request->vendor_user_name_id[$serviceId];
                foreach ($usernames as $username) {
                    // Handle username
                    if (is_numeric($username)) {

                        $vendorUsernameId = $username;
                    } else {

                        $newUsername = VendorUserName::create([
                            'name' => $username,
                            'vendor_id' => $vendor->id,
                        ]);
                        $vendorUsernameId = $newUsername->id;
                    }

                    // Save vendor service
                    DB::table('vendor_services')->insert([
                        'vendor_user_name_id' => $vendorUsernameId, 
                        'service_type_id' => $serviceId,
                        'vendor_id' => $vendor->id,
                    ]);


                    // Save communities
                    if (isset($request->served_communities[$serviceId])) {

                        foreach ($request->served_communities[$serviceId] as $communityId) {

                            DB::table('community_vendors')->insert([
                                'vendor_username_id' => $vendorUsernameId, 
                                'community_id' => $communityId,
                                'service_type_id' => $serviceId,
                                'vendor_id' => $vendor->id,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect('/vending-history')
            ->with('message', 'New Vending Point Added Successfully!');
    }


    /**
     * Show the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { 
        $town = null; 
        $community = null;
        $vendorRegion = null;
        $vendorUserName = null;

        $vendor = Vendor::findOrFail($id);
        if($vendor->vendor_region_id) $vendorRegion = VendorRegion::findOrFail($vendor->vendor_region_id);
        if($vendor->vendor_username_id) $vendorUserName = VendorUserName::findOrFail($vendor->vendor_username_id);

        if($vendor->community_id) $community = Community::findOrFail($vendor->community_id);
        if($vendor->town_id) $town = Town::findOrFail($vendor->town_id);

        $vendorCommunities = DB::table('community_vendors')
            ->join('communities', 'community_vendors.community_id', 'communities.id')
            ->join('service_types', 'community_vendors.service_type_id','service_types.id')
            ->where('community_vendors.vendor_id', $vendor->id)
            ->select('communities.english_name', 'service_types.service_name')
            ->get();

        $vendorServices = DB::table('vendor_services')
            ->join('service_types', 'vendor_services.service_type_id', 'service_types.id')
            ->join('vendor_user_names', 'vendor_services.vendor_user_name_id','vendor_user_names.id')
            ->where('vendor_services.vendor_id', $vendor->id)
            ->select('service_types.service_name', 'vendor_user_names.name')
            ->get();

        $response['town'] = $town;
        $response['community'] = $community;
        $response['vendor'] = $vendor;
        $response['vendorRegion'] = $vendorRegion;
        $response['vendorUserName'] = $vendorUserName;
        $response['vendorCommunities'] = $vendorCommunities;
        $response['vendorServices'] = $vendorServices;

        return response()->json($response);
    }

    /**
     * View Edit page.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vendingPoint = Vendor::findOrFail($id);

        $communities = Community::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->get();

        $vendorRegions = VendorRegion::where('is_archived', 0)->get();

        $vendorUsers = VendorUserName::all();

        $towns = Town::where('is_archived', 0)
            ->orderBy('english_name', 'ASC')
            ->get();

        $services = ServiceType::where('is_archived', 0)
            ->where("service_name", "!=", "Camera")
            ->get();

        // vendor services
        $vendorData = DB::table('vendor_services')
            ->join('vendor_user_names', 'vendor_services.vendor_user_name_id', '=', 'vendor_user_names.id')
            ->where('vendor_services.vendor_id', $vendingPoint->id)
            ->select(
                'vendor_services.service_type_id',
                'vendor_services.vendor_user_name_id'
            )
            ->get()
            ->groupBy('service_type_id');

        // communities per username per service
        $vendorCommunities = DB::table('community_vendors')
            ->where('vendor_id', $vendingPoint->id)
            ->where('is_archived', 0)
            ->get()
            ->groupBy(['service_type_id', 'vendor_username_id']);

        return view('vendor.edit', compact(
            'vendingPoint',
            'communities',
            'vendorRegions',
            'vendorUsers',
            'towns',
            'services',
            'vendorData',
            'vendorCommunities'
        ));
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $vendor = Vendor::findOrFail($id);

        $vendor->english_name = $request->english_name;
        $vendor->arabic_name = $request->arabic_name;
        if($request->vendor_region_id) $vendor->vendor_region_id = $request->vendor_region_id;
        if($request->phone_number) $vendor->phone_number = $request->phone_number;
        if($request->status) $vendor->status = $request->status;
        if($request->additional_phone_number) $vendor->additional_phone_number = $request->additional_phone_number;
        if($request->notes) $vendor->notes = $request->notes;

        // $vendor->community_id = null;
        // $vendor->town_id = null;

        if ($request->community_town === "community") {
            $vendor->community_id = $request->community_town_id;
        } elseif ($request->community_town === "town") {
            $vendor->town_id = $request->community_town_id;
        }

        $vendor->save();

        DB::table('vendor_services')->where('vendor_id', $vendor->id)->delete();
        DB::table('community_vendors')->where('vendor_id', $vendor->id)->delete();

        if ($request->has('service_type_id')) {

            foreach ($request->service_type_id as $serviceId) {

                if (!isset($request->vendor_user_name_id[$serviceId])) {
                    continue;
                }

                foreach ($request->vendor_user_name_id[$serviceId] as $usernameId) {

                    if (!is_numeric($usernameId)) {
                        $newUser = VendorUserName::create([
                            'name' => $usernameId,
                            'vendor_id' => $vendor->id,
                        ]);

                        // Move 'new' communities to the actual new username ID
                        if (isset($request->served_communities[$serviceId]['new'])) {
                            $request->merge([
                                'served_communities' => array_replace_recursive(
                                    $request->served_communities,
                                    [
                                        $serviceId => [
                                            $newUser->id => $request->served_communities[$serviceId]['new']
                                        ]
                                    ]
                                )
                            ]);

                            unset($request->served_communities[$serviceId]['new']);
                        }

                        $usernameId = $newUser->id;
                    }

                    DB::table('vendor_services')->insert([
                        'vendor_user_name_id' => $usernameId,
                        'service_type_id' => $serviceId,
                        'vendor_id' => $vendor->id,
                    ]);

                    if (
                        isset($request->served_communities[$serviceId][$usernameId])
                    ) {
                        foreach ($request->served_communities[$serviceId][$usernameId] as $communityId) {

                            DB::table('community_vendors')->insert([
                                'vendor_username_id' => $usernameId,
                                'service_type_id' => $serviceId,
                                'vendor_id' => $vendor->id,
                                'community_id' => $communityId,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect('/vending-history')
            ->with('message', 'Vendor updated successfully!');
    }

    /**
     * Get households by community_id.
     *
     * @param  String $is_household
     * @return \Illuminate\Http\Response
     */
    public function getVendingPointPlace(String $community_town)
    {

        $html = "<option disabled selected>Choose one...</option>";



        if($community_town == "community") {



            $places = Community::where('is_archived', 0)

                ->orderBy('english_name', 'ASC')

                ->get();

        } else if($community_town == "town") {



            $places = Town::where('is_archived', 0)

                ->orderBy('english_name', 'ASC')

                ->get(); 

        }

        

        foreach($places as $place) {



            $html .= '<option value="'.$place->id.'">'.$place->english_name.'</option>';

        }



        return response()->json([

            'html' => $html

        ]);

    }



    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteVendor(Request $request)
    {
        $id = $request->id;
        $vendor = Vendor::find($id);

        if($vendor) {

            $vendor->is_archived = 1;
            $vendor->save();

            $response['success'] = 1;
            $response['msg'] = 'Vending Point Deleted successfully'; 
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
    public function deleteServedCommunity(Request $request)
    { 
        $id = $request->id;
        $communityVendingPoint = CommunityVendor::find($id);

        if($communityVendingPoint) {

            $communityVendingPoint->is_archived = 1;
            $communityVendingPoint->save();
            $response['success'] = 1;
            $response['msg'] = 'Served Community Deleted successfully'; 
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

        return Excel::download(new VendingPointAndHistoryExport($request), 'Vending Points & Collecting Money History.xlsx');
    }
}