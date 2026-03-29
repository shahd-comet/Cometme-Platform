<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Replacement;
use Yajra\DataTables\DataTables;
use App\Models\SubRegion;
use App\Models\Community;
use App\Models\Region;
use App\Models\CameraCommunity;
use App\Models\Camera;
use App\Models\NvrCamera;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReplacementsExport;
use App\Models\CameraReplacementIncident;
use App\Models\Donor;

class ReplacementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Replacement::with(['cameraCommunity.community', 'camera', 'nvrCamera'])->latest();

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
                $query->whereDate('date_of_replacement', $request->date_filter);
            }

            if ($request->filled('camera_replacement_incident_id')) {
                $query->where('camera_replacement_incident_id', $request->camera_replacement_incident_id);
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
                ->addColumn('incident_type', function ($row) {
                    return $row->cameraReplacementIncident->english_name ?? 'N/A';
                })
                ->addColumn('compound', function ($row) {
                    return $row->compound ? $row->compound->english_name : '';
                })
                ->addColumn('donors', function ($row) {
                    $donors = $row->donors()->with('donor')->get();
                    return $donors->pluck('donor.donor_name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $updateButton = "<a type='button' class='updateCamera' data-id='".$row->id."'>
                                        <i class='fa-solid fa-pen-to-square text-success'></i>
                                     </a>";
                    $deleteButton = "<a type='button' class='deleteCamera' data-id='".$row->id."'>
                                        <i class='fa-solid fa-trash text-danger'></i>
                                     </a>";
                    return $updateButton . " " . $deleteButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $cameraCommunities = CameraCommunity::with(['community'])->get();

        $regions = Region::whereHas('communities.cameraCommunities')->get();

        $communities = Community::whereHas('cameraCommunities')->get();

        $cameras = Camera::all();
        $nvrCameras = NvrCamera::all();
        $subRegions = SubRegion::all();
        $cameraReplacementIncidents = CameraReplacementIncident::all();
        $compounds = \App\Models\Compound::all();
        $donors = Donor::all();

        return view('services.camera.replacements.index', compact(
            'cameraCommunities',
            'cameras',
            'nvrCameras',
            'subRegions',
            'communities',
            'regions',
            'cameraReplacementIncidents',
            'compounds',
            'donors'
        ));
    }

    public function create()
    {
        $cameraCommunities = CameraCommunity::with(['community'])->get();
        $cameras = Camera::all();
        $nvrCameras = NvrCamera::all();
        $cameraReplacementIncidents = CameraReplacementIncident::all();
        $compounds = \App\Models\Compound::all();
        $donors = Donor::all();
        return view('services.camera.replacements.create', compact(
            'cameraCommunities',
            'cameras',
            'nvrCameras',
            'cameraReplacementIncidents',
            'compounds',
            'donors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'camera_community_id' => 'required|exists:camera_communities,id',
            'date_of_replacement' => 'required|date',
            'damaged_camera_count' => 'required|integer|min:0',
            'new_camera_count' => 'required|integer|min:0',
            'camera_id' => 'required|exists:cameras,id',
            'nvr_camera_id' => 'nullable|exists:nvr_cameras,id',
            'number_of_nvr' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'camera_replacement_incident_id' => 'nullable|exists:camera_replacement_incidents,id',
            'compound_id' => 'nullable|exists:compounds,id',
            'damaged_sd_card_count' => 'nullable|integer|min:0',
            'new_sd_card_count' => 'nullable|integer|min:0',
            'donor_ids' => 'nullable|array',
            'donor_ids.*' => 'exists:donors,id',
        ]);
        
        // تعيين القيمة الافتراضية إلى 0 إذا كانت فارغة
        $validated['number_of_nvr'] = $validated['number_of_nvr'] ?? 0;
        
        $replacement = Replacement::create($validated);
        
        // حفظ المتبرعين
        if ($request->has('donor_ids') && is_array($request->donor_ids)) {
            foreach ($request->donor_ids as $donorId) {
                $replacement->donors()->create(['donor_id' => $donorId]);
            }
        }
        
        return redirect()->route('camera.all')->with('message', 'Replacement created successfully.');
    }

    public function edit($id)
    {
        $replacement = Replacement::with('donors')->findOrFail($id);
        $cameraCommunities = CameraCommunity::with('community')->get();
        $cameras = Camera::all();
        $nvrs = NvrCamera::all();
        $cameraReplacementIncidents = CameraReplacementIncident::all();
        $compounds = \App\Models\Compound::all();
        $donors = Donor::all();
        return view('services.camera.replacements.edit', compact(
            'replacement',
            'cameraCommunities',
            'cameras',
            'nvrs',
            'cameraReplacementIncidents',
            'compounds',
            'donors'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'camera_community_id' => 'required|exists:camera_communities,id',
            'date_of_replacement' => 'required|date',
            'damaged_camera_count' => 'required|integer|min:0',
            'new_camera_count' => 'required|integer|min:0',
            'camera_id' => 'required|exists:cameras,id',
            'nvr_camera_id' => 'nullable|exists:nvr_cameras,id',
            'number_of_nvr' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'camera_replacement_incident_id' => 'nullable|exists:camera_replacement_incidents,id',
            'compound_id' => 'nullable|exists:compounds,id',
            'damaged_sd_card_count' => 'nullable|integer|min:0',
            'new_sd_card_count' => 'nullable|integer|min:0',
            'donor_ids' => 'nullable|array',
            'donor_ids.*' => 'exists:donors,id',
        ]);
        
        // تعيين القيمة الافتراضية إلى 0 إذا كانت فارغة
        $validated['number_of_nvr'] = $validated['number_of_nvr'] ?? 0;
        
        $replacement = Replacement::findOrFail($id);
        $replacement->update($validated);
        
        // تحديث المتبرعين
        $replacement->donors()->delete(); // حذف المتبرعين الحاليين
        if ($request->has('donor_ids') && is_array($request->donor_ids)) {
            foreach ($request->donor_ids as $donorId) {
                $replacement->donors()->create(['donor_id' => $donorId]);
            }
        }
        
        return redirect()->route('camera.all')->with('message', 'Replacement updated successfully.');
    }

    public function destroy(Request $request)
    {
        $replacement = Replacement::find($request->id);
        if ($replacement) {
            $replacement->delete();
            return response()->json(['success' => 1, 'msg' => 'Replacement deleted successfully.']);
        } else {
            return response()->json(['success' => 0, 'msg' => 'Replacement not found.']);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new ReplacementsExport($request), 'camera_replacements.xlsx');
    }
}
