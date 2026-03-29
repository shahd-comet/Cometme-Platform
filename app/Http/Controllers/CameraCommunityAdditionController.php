<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CameraCommunityAddition;
use Yajra\DataTables\DataTables;
use App\Models\SubRegion;
use App\Models\Community;
use App\Models\Region;
use App\Models\CameraCommunity;
use App\Models\Camera;
use App\Models\NvrCamera;
use App\Models\Donor;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CameraAdditionsExport;

class CameraCommunityAdditionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CameraCommunityAddition::with(['cameraCommunity.community', 'camera', 'nvrCamera'])->latest();

            if ($request->filled('region_filter')) {
                $regionId = $request->region_filter;
                $query->whereHas('cameraCommunity.community', function ($q) use ($regionId) {
                    $q->where('region_id', $regionId);
                });
            }

            if ($request->filled('community_filter')) {
                $query->whereHas('cameraCommunity', function ($q) use ($request) {
                    $q->where('community_id', $request->community_filter);
                });
            }

            if ($request->filled('date_filter')) {
                $query->whereDate('date_of_addition', $request->date_filter);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('community', function ($row) {
                    return $row->cameraCommunity->community->english_name ?? 'N/A';
                })
                ->addColumn('camera_type', function ($row) {
                    return $row->camera->model ?? 'N/A';
                })
                ->addColumn('nvr', function ($row) {
                    return $row->nvrCamera->model ?? 'None';
                })
                ->addColumn('compound', function ($row) {
                    return $row->compound ? $row->compound->english_name : '';
                })
                ->addColumn('donors', function ($row) {
                    $donors = $row->donors()->with('donor')->get();
                    return $donors->pluck('donor.donor_name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $updateButton = "<a type='button' class='updateAddition' data-id='".$row->id."'>
                                        <i class='fa-solid fa-pen-to-square text-success'></i>
                                     </a>";
                    $deleteButton = "<a type='button' class='deleteAddition' data-id='".$row->id."'>
                                        <i class='fa-solid fa-trash text-danger'></i>
                                     </a>";
                    return $updateButton . " " . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $cameraCommunities = CameraCommunity::with('community')->get();
        $cameras = Camera::all();
        $nvrs = NvrCamera::all();

        $communities = Community::whereIn('id', function ($q) {
            $q->select('community_id')
              ->from('camera_communities')
              ->whereNotNull('community_id');
        })->get();

        $regions = Region::whereIn('id', function ($q) {
            $q->select('region_id')
              ->from('communities')
              ->whereIn('id', function ($q2) {
                  $q2->select('community_id')
                     ->from('camera_communities')
                     ->whereNotNull('community_id');
              });
        })->get();

        $subRegions = SubRegion::all();
        $compounds = \App\Models\Compound::all();
        $donors = Donor::all();

        return view('services.camera.additions.index', compact(
            'cameraCommunities',
            'cameras',
            'nvrs',
            'subRegions',
            'communities',
            'regions',
            'compounds',
            'donors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'camera_community_id' => 'required|exists:camera_communities,id',
            'date_of_addition' => 'required|date',
            'number_of_cameras' => 'required|integer|min:0',
            'camera_id' => 'required|exists:cameras,id',
            'nvr_camera_id' => 'nullable|exists:nvr_cameras,id',
            'number_of_nvr' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'compound_id' => 'nullable|exists:compounds,id',
            'sd_card_number' => 'nullable|numeric',
            'donor_ids' => 'nullable|array',
            'donor_ids.*' => 'exists:donors,id',
        ]);
        
        // تعيين القيمة الافتراضية إلى 0 إذا كانت فارغة
        $validated['number_of_nvr'] = $validated['number_of_nvr'] ?? 0;
        
        $addition = CameraCommunityAddition::create($validated);
        
        // حفظ المتبرعين
        if ($request->has('donor_ids') && is_array($request->donor_ids)) {
            foreach ($request->donor_ids as $donorId) {
                $addition->donors()->create(['donor_id' => $donorId]);
            }
        }
        
        return redirect()->route('camera.all')->with('message', 'Camera addition created successfully.');
    }

    public function edit($id)
    {
        $addition = CameraCommunityAddition::with('donors')->findOrFail($id);
        $cameraCommunities = CameraCommunity::with('community')->get();
        $cameras = Camera::all();
        $nvrs = NvrCamera::all();
        $communities = Community::all();
        $compounds = \App\Models\Compound::all();
        $donors = Donor::all();
        return view('services.camera.additions.edit', compact(
            'addition',
            'cameraCommunities',
            'cameras',
            'nvrs',
            'communities',
            'compounds',
            'donors'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'camera_community_id' => 'required|exists:camera_communities,id',
            'date_of_addition' => 'required|date',
            'number_of_cameras' => 'required|integer|min:0',
            'camera_id' => 'required|exists:cameras,id',
            'nvr_camera_id' => 'nullable|exists:nvr_cameras,id',
            'number_of_nvr' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'compound_id' => 'nullable|exists:compounds,id',
            'sd_card_number' => 'nullable|numeric',
            'donor_ids' => 'nullable|array',
            'donor_ids.*' => 'exists:donors,id',
        ]);
        
        // تعيين القيمة الافتراضية إلى 0 إذا كانت فارغة
        $validated['number_of_nvr'] = $validated['number_of_nvr'] ?? 0;
        
        $addition = CameraCommunityAddition::findOrFail($id);
        $addition->update($validated);
        
        // تحديث المتبرعين
        $addition->donors()->delete(); // حذف المتبرعين الحاليين
        if ($request->has('donor_ids') && is_array($request->donor_ids)) {
            foreach ($request->donor_ids as $donorId) {
                $addition->donors()->create(['donor_id' => $donorId]);
            }
        }
        
        return redirect()->route('camera.all')->with('message', 'Camera addition updated successfully.');
    }

    public function destroy(Request $request)
    {
        $addition = CameraCommunityAddition::find($request->id);
        if ($addition) {
            $addition->delete();
            return response()->json(['success' => 1, 'msg' => 'Camera addition deleted successfully.']);
        } else {
            return response()->json(['success' => 0, 'msg' => 'Camera addition not found.']);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new CameraAdditionsExport($request), 'camera_additions.xlsx');
    }
}
