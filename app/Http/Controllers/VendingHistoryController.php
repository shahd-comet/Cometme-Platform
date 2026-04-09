<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use DB; 

use Route;
use App\Models\Community;
use App\Models\CommunityVendor;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\PublicStructure;
use App\Models\Region;
use App\Models\Town;
use App\Models\VendorUserName;
use App\Models\VendorRegion;
use App\Models\Vendor;
use App\Models\VendorService;
use App\Models\VendingHistory;
use App\Imports\ImportVendingHistory;
use App\Helpers\SequenceHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Image;
use DataTables;
use Excel;

class VendingHistoryController extends Controller
{
    // This method for generating the action buttons
    private function generateActionButtons($row)
    {
        $viewButton = "<a type='button' class='viewVendingHistory' data-id='".$row->id."' data-bs-toggle='modal' data-bs-target='#viewVendingHistoryModal' ><i class='fa-solid fa-eye text-info'></i></a>";
        $updateButton = "<a type='button' class='updateAllVendingHistory' data-id='".$row->id."' ><i class='fa-solid fa-pen-to-square text-success'></i></a>";
        $deleteButton = "<a type='button' class='deleteAllVendingHistory' data-id='".$row->id."'><i class='fa-solid fa-trash text-danger'></i></a>";
        
        if(Auth::guard('user')->user()->user_type_id == 1 || 
            Auth::guard('user')->user()->user_type_id == 2 ||
            Auth::guard('user')->user()->user_type_id == 3 ||
            Auth::guard('user')->user()->user_type_id == 4) 
        {
                
            return $viewButton." ". $updateButton." ".$deleteButton;
        } else return $viewButton;
    }

    /**
     * Getting the total for different agents
     *
     * @return \Illuminate\Http\Response
     */
    public function getCounts()
    {
        $vendingHistoryCount = VendingHistory::where("is_archived", 0)->count();

        $vendorsCount = Vendor::where("is_archived", 0)->count();

        return response()->json([
            'vendingHistoryCount' => $vendingHistoryCount,
            'vendorsCount'       => $vendorsCount
        ]);
    }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Run the following code in order

        // #1
        // $vendors = Vendor::all();
        // foreach($vendors as $vendor) {

        //     $vendor->status = "Active";
        //     $vendor->save();

        //     $exisitVendorService = VendorService::where("vendor_id", $vendor->id)
        //         ->where("service_type_id", 1)
        //         ->first();

        //     if(!$exisitVendorService) {

        //         $newVendorService = new VendorService();
        //         $newVendorService->vendor_id = $vendor->id;
        //         $newVendorService->service_type_id = 1;
        //         $newVendorService->vendor_user_name_id = $vendor->vendor_username_id;
        //         $newVendorService->save();
        //     }
        // }

        
        // #2
        // $allCommunityVendors = CommunityVendor::all();
        // foreach($allCommunityVendors as $allCommunityVendor) {

        //     $vendorService = VendorService::where("service_type_id", $allCommunityVendor->service_type_id)
        //         ->where("vendor_user_name_id", $allCommunityVendor->vendor_username_id)
        //         ->first();
        //     $allCommunityVendor->service_type_id = 1;
        //     if($vendorService) $allCommunityVendor->vendor_id = $vendorService->vendor_id; 
        //     $allCommunityVendor->save();
        // }

        // #3
        // $allVendorUsernames = VendorUserName::all();
        // foreach($allVendorUsernames as $allVendorUsername) {

        //     $vendor = Vendor::where("vendor_username_id", $allVendorUsername->id)->first();
        //     if($vendor) {

        //         $allVendorUsername->vendor_id = $vendor->id;
        //         $allVendorUsername->save();
        //     }
        // }
        
        $regionFilter = $request->input('region_filter');
        $communityFilter = $request->input('community_filter');
        $townFilter = $request->input('town_filter');
        $serviceFilter = $request->input('service_filter');
        $vendorFilter = $request->input('vendor_filter');

        if (Auth::guard('user')->user() != null) {

            if ($request->ajax()) {

                $data = DB::table('vending_histories')
                    ->join('vendor_services', 'vending_histories.vendor_service_id', 'vendor_services.id')
                    ->join('vendors', 'vendor_services.vendor_id', 'vendors.id')
                    ->leftJoin('service_types', 'vendor_services.service_type_id', 'service_types.id')
                    ->leftJoin('users', 'vending_histories.user_id', 'users.id')
                    ->leftJoin('vendor_regions', 'vendors.vendor_region_id', 'vendor_regions.id')
                    ->leftJoin('communities', 'vendors.community_id', 'communities.id')
                    ->leftJoin('towns', 'vendors.town_id', 'towns.id')
                    ->where('vending_histories.is_archived', 0);

                $data->when($regionFilter, fn($q) => $q->where('vendor_regions.id', $regionFilter))
                    ->when($communityFilter, fn($q) => $q->where('communities.id', $communityFilter))
                    ->when($townFilter, fn($q) => $q->where('towns.id', $townFilter))
                    ->when($serviceFilter, fn($q) => $q->where('service_types.id', $serviceFilter))
                    ->when($vendorFilter, fn($q) => $q->where('vendors.id', $vendorFilter));

                $search = $request->input('search.value'); 

                if (!empty($search)) {
                    $data->where(function ($w) use ($search) {
                        $w->where('service_types.service_name', 'LIKE', "%$search%")
                        ->orWhere('vendors.english_name', 'LIKE', "%$search%")
                        ->orWhere('vendors.arabic_name', 'LIKE', "%$search%")
                        ->orWhere('vending_histories.visit_date', 'LIKE', "%$search%")
                        ->orWhere('vending_histories.total_amount_due', 'LIKE', "%$search%")
                        ->orWhere('vending_histories.amount_collected', 'LIKE', "%$search%")
                        ->orWhere('users.name', 'LIKE', "%$search%");
                    });
                }

                $totalRecords = DB::table('vending_histories')->where('vending_histories.is_archived', 0)->count();

                $filteredRecords = (clone $data)->count();

                $data = $data->select(
                    'vending_histories.visit_date', 
                    'vending_histories.total_amount_due',
                    'vending_histories.collecting_date_from',
                    'vending_histories.collecting_date_to',
                    'vending_histories.id as id', 'vending_histories.created_at as created_at', 
                    'vending_histories.updated_at as updated_at', 
                    'service_types.service_name',
                    'vendors.english_name as vendor', 
                    'users.name as user_name',
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

            $communities = Community::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();
            $regions = Region::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get(); 
            $vendors = Vendor::where('is_archived', 0)->get();
            $vendorRegions = VendorRegion::where('is_archived', 0)->get();
            $vendorUsers = VendorUserName::all();
            $towns = Town::where('is_archived', 0)
                ->orderBy('english_name', 'ASC')
                ->get();
            $services = ServiceType::where('is_archived', 0)->where("service_name", "!=", "Camera")->get();
            $users = User::where('is_archived', 0)->get();

            return view('vendor.index', compact('communities', 'regions', 'vendorRegions', 'vendorUsers',
                'towns', 'services', 'vendors', 'users')
            );
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
        $vendingHistory = new VendingHistory();
        $vendingHistory->vendor_id = $request->vendor_id;
        $vendorService = VendorService::where("vendor_id", $request->vendor_id)
            ->where("service_type_id", $request->service_type_id)
            ->first();
        $vendingHistory->vendor_service_id = $vendorService->id;
        $vendingHistory->visit_date = $request->visit_date;
        $vendingHistory->user_id = $request->user_id;
        $vendingHistory->collecting_date_from = $request->collecting_date_from;
        $vendingHistory->collecting_date_to = $request->collecting_date_to;
        $vendingHistory->total_amount_due = $request->total_amount_due;
        $vendingHistory->amount_collected = $request->amount_collected;
        $vendingHistory->remaining_balance = $request->remaining_balance;
        $vendingHistory->notes = $request->notes;
        $vendingHistory->save();

        return redirect('/vending-history')
            ->with('message', 'New Vending History Added Successfully!');
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
        $vendorService = null;
        $user = null;

        $vendingHistory = VendingHistory::findOrFail($id); 
        $vendorService = VendorService::findOrFail($vendingHistory->vendor_service_id);
        
        if($vendorService->vendor_user_name_id) $vendorUserName = VendorUserName::findOrFail($vendorService->vendor_user_name_id);

        $vendor = Vendor::findOrFail($vendingHistory->vendor_id); 
        $vendorRegion = VendorRegion::findOrFail($vendor->vendor_region_id);
        
        if($vendor->community_id) $community = Community::where("id", $vendor->community_id)->first();
        if($vendor->town_id) $town = Town::where("id", $vendor->town_id)->first();

        if($vendingHistory->user_id) $user = User::where("id", $vendingHistory->user_id)->first();

        $response['town'] = $town;
        $response['vendingHistory'] = $vendingHistory;
        $response['vendorService'] = $vendorService;
        $response['community'] = $community;
        $response['vendor'] = $vendor;
        $response['vendorRegion'] = $vendorRegion;
        $response['vendorUserName'] = $vendorUserName;
        $response['user'] = $user;

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
        $vendingHistory = VendingHistory::findOrFail($id);

        $users = User::where('is_archived', 0)->get();

        return view('vendor.history.edit', compact('vendingHistory', 'users'));
    }

    /**
     * Update an existing resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request, int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $vendingHistory = VendingHistory::find($id);
        $vendingHistory->visit_date = $request->visit_date;
        if($request->user_id) $vendingHistory->user_id = $request->user_id;
        $vendingHistory->collecting_date_from = $request->collecting_date_from;
        $vendingHistory->collecting_date_to = $request->collecting_date_to;
        $vendingHistory->total_amount_due = $request->total_amount_due;
        $vendingHistory->amount_collected = $request->amount_collected;
        $vendingHistory->remaining_balance = $request->remaining_balance;
        $vendingHistory->notes = $request->notes;
        $vendingHistory->save();

        return redirect('/vending-history')->with('message', 'Vending Visit Record Updated Successfully!');
    }

    /**
     * Delete a resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteVendingHistory(Request $request)
    {
        $id = $request->id;

        $vendingHistory = VendingHistory::find($id);
        if($vendingHistory) {

            $vendingHistory->is_archived = 1;
            $vendingHistory->save();

            $response['success'] = 1;
            $response['msg'] = 'Vending History Deleted successfully'; 
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
    public function import(Request $request)
    {
        // Check if file exists in the request
        if (!$request->hasFile('excel_file')) {

            return redirect()->back()->with('error', 'No file uploaded.');
        }

        $file = $request->file('excel_file');

        // Check if the file is valid
        if (!$file->isValid()) {

            return redirect()->back()->with('error', 'File upload failed.');
        }

        // Validate extension
        $extension = $file->getClientOriginalExtension();
        $allowedExtensions = ['xlsx', 'xls', 'csv'];

        if (!in_array($extension, $allowedExtensions)) {

            return redirect()->back()->with('error', 'Invalid file format. Please upload an Excel or CSV file.');
        }

        try {

            // Clear the existing table before import

            DB::table('vending_histories')->truncate();

            // Import data
            Excel::import(new ImportVendingHistory, $file);

            return redirect()->back()->with('success', 'Vedning History Data Imported Successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            // Handles row validation errors from the import

            $failures = $e->failures();
            return redirect()->back()->with('error', 'Validation failed during import: ' . $failures[0]->errors()[0]);
        } catch (\Exception $e) {

            // Handles all other exceptions
            return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Get resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getServiceByVendor($vendor_id)
    {
        if (!$vendor_id) {

            $html = '<option value="">Choose One...</option>';
        } else {

            $html = '<option value="">Select...</option>';

            $services = DB::table('vendor_services')
                ->join('service_types', 'vendor_services.service_type_id', 'service_types.id')
                ->where("vendor_services.vendor_id", $vendor_id)
                ->select('service_types.id', 'service_types.service_name')
                ->get();

            foreach ($services as $service) {

                $html .= '<option value="'.$service->id.'">'.$service->service_name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
    }
}