<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CameraCommunityReturned;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use Yajra\DataTables\DataTables;
use App\Models\SubRegion;
use App\Models\Community;
use App\Models\Region;
use App\Models\CameraCommunity;
use App\Models\Camera;
use App\Models\NvrCamera;
use App\Models\Repository;
use App\Models\Compound;

class CameraReturnedController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CameraCommunityReturned::with([
                'cameraCommunity.community',
                'cameraCommunity.repository',
                'cameraCommunity.compound',
                'cameraCommunity.household',
                'camera',
                'nvrCamera'
            ])->latest();

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
                $query->whereDate('date', $request->date_filter);
            }

            try {
                return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('community', function ($row) {
                    $cc = $row->cameraCommunity ?? null;
                    if (!$cc) return 'Unknown';

                    $community = $cc->community ? ($cc->community->english_name ?: $cc->community->arabic_name ?: null) : null;
                    $repository = $cc->repository ? ($cc->repository->name ?: null) : null;
                    $compound = $cc->compound ? ($cc->compound->english_name ?: null) : null;
                    $household = $cc->household ? ($cc->household->english_name ?: null) : null;

                    $parts = [];
                    if ($community) $parts[] = $community;
                    if ($repository) $parts[] = $repository;

                    if (!empty($parts)) {
                        $base = implode(' / ', $parts);
                        return $compound ? ($base . ' - ' . $compound) : $base;
                    }

                    if ($household) return $household;
                    if ($compound) return $compound;
                    if ($repository) return $repository;

                    return 'Unknown';
                })
                ->addColumn('repository', function ($row) {
                    return $row->repository->name ?? 'N/A';
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
                ->addColumn('action', function ($row) {
                    $editUrl = route('camera-returned.edit', $row->id);
                    $editButton = "<a href='".$editUrl."' class='me-2' title='Edit'>
                                        <i class='fa-solid fa-pen-to-square text-success'></i>
                                     </a>";
                    $deleteButton = "<a type='button' class='deleteReturned' data-id='".$row->id."' title='Delete'>
                                        <i class='fa-solid fa-trash text-danger'></i>
                                     </a>";
                    return $editButton . $deleteButton ;
                })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'DataTables processing error',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }

        $cameraCommunities = CameraCommunity::with(['community', 'repository', 'compound'])->get();
        $regions = Region::whereHas('communities.cameraCommunities')->get();
        $communities = Community::whereHas('cameraCommunities')->get();
        $cameras = Camera::all();
        $nvrCameras = NvrCamera::all();
        $subRegions = SubRegion::all();
        $repositories = Repository::all();
        // $compounds = \App\Models\Compound::all();

        // "CommunityName / RepositoryName - CompoundName" (compound part optional)
        foreach ($cameraCommunities as $cc) {
            $community = $cc->community ?? null;
            $compound = $cc->compound ?? null;
            $repository = $cc->repository ?? null;
            $household = $cc->household ?? null;
            $communityName = $community ? ($community->english_name ?: $community->arabic_name ?: null) : null;
            $repositoryName = $repository ? ($repository->name ?: 'Repository #' . $repository->id) : null;
            $compoundName = $compound ? ($compound->english_name ?: 'Compound #' . $compound->id) : null;

            $parts = [];
            if ($communityName) $parts[] = $communityName;
            if ($repositoryName) $parts[] = $repositoryName;

            if (!empty($parts)) {
                // join community and repository with ' / '
                $base = implode(' / ', $parts);
                if ($compoundName) {
                    $cc->display_name = $base . ' - ' . $compoundName;
                } else {
                    $cc->display_name = $base;
                }
                continue;
            }

            if (!$communityName && $repository) {
                $repoDisplay = $repository->name ?: 'Repository #' . $repository->id;
                if ($compoundName) {
                    $cc->display_name = $repoDisplay . ' - <i>' . $compoundName . '</i>';
                } else {
                    $cc->display_name = $repoDisplay;
                }
                continue;
            }

            // if no community/repository, try household, then compound, then repository name alone
            if ($household && !empty($household->english_name)) {
                $cc->display_name = $household->english_name;
                continue;
            }

            if ($compoundName) {
                $cc->display_name = $compoundName;
                continue;
            }

            if ($repositoryName) {
                $cc->display_name = $repositoryName;
                continue;
            }

            // final fallback: include raw ids so the user can see why it's unknown (testing purpose)
            $cc->display_name = 'Unknown (cc:'.$cc->id.' c:'.$cc->community_id.' comp:'.$cc->compound_id.' repo:'.$cc->repository_id.' hh:'.($cc->household_id ?? 'NULL').')';
        }

        return view('services.camera.returned.index', compact(
            'cameraCommunities',
            'cameras',
            'nvrCameras',
            'repositories',
            'subRegions',
            'communities',
            'regions',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'camera_community_id' => 'required|exists:camera_communities,id',
            'date_of_return' => 'required|date',
            'camera_id' => 'required|exists:cameras,id',
            'number_of_cameras' => 'required|integer|min:0',
            'nvr_camera_id' => 'nullable|exists:nvr_cameras,id',
            'number_of_nvrs' => 'nullable|integer|min:0',
            'sd_card_number' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'repository_id' => 'nullable|exists:repositories,id',
            'status' => 'nullable|in:0,1,2',
        ]);

        // Map form fields to model fields
        $data = [
            'camera_community_id' => $validated['camera_community_id'],
            'date' => $validated['date_of_return'],
            'camera_id' => $validated['camera_id'],
            'number_of_cameras' => $validated['number_of_cameras'],
            'nvr_camera_id' => $validated['nvr_camera_id'] ?? null,
            'number_of_nvr' => $validated['number_of_nvrs'] ?? 0,
            'sd_card_number' => $validated['sd_card_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'repository_id' => $validated['repository_id'] ?? null,
            'status' => $validated['status'] ?? $request->input('status', 0),
        ];

        $returned = CameraCommunityReturned::create($data);

        return redirect()->to(route('camera.all') . '?tab=returned')->with('message', 'Returned camera record created successfully.');
    }

    public function destroy(Request $request)
    {
        $record = CameraCommunityReturned::find($request->id);
        if ($record) {
            $record->delete();
            return response()->json(['success' => 1, 'msg' => 'Returned record deleted successfully.']);
        } else {
            return response()->json(['success' => 0, 'msg' => 'Record not found.']);
        }
    }

    public function edit($id)
    {
        $returned = CameraCommunityReturned::with(['cameraCommunity.community','cameraCommunity.repository','cameraCommunity.compound','cameraCommunity.household','camera','nvrCamera'])->findOrFail($id);

        $cameraCommunities = CameraCommunity::with(['community', 'repository', 'compound', 'household'])->get();
        $cameras = Camera::all();
        $nvrCameras = NvrCamera::all();
        $repositories = Repository::all();
        $compounds = Compound::all();

        return view('services.camera.returned.edit', compact('returned','cameraCommunities','cameras','nvrCameras','repositories','compounds'));
    }

    public function update(Request $request, $id)
    {
        $record = CameraCommunityReturned::findOrFail($id);

        $validated = $request->validate([
            'camera_community_id' => 'required|exists:camera_communities,id',
            'date_of_return' => 'required|date',
            'camera_id' => 'required|exists:cameras,id',
            'number_of_cameras' => 'required|integer|min:0',
            'nvr_camera_id' => 'nullable|exists:nvr_cameras,id',
            'number_of_nvrs' => 'nullable|integer|min:0',
            'sd_card_number' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'repository_id' => 'nullable|exists:repositories,id',
            'status' => 'nullable|in:0,1,2',
        ]);

        $data = [
            'camera_community_id' => $validated['camera_community_id'],
            'date' => $validated['date_of_return'],
            'camera_id' => $validated['camera_id'],
            'number_of_cameras' => $validated['number_of_cameras'],
            'nvr_camera_id' => $validated['nvr_camera_id'] ?? null,
            'number_of_nvr' => $validated['number_of_nvrs'] ?? 0,
            'sd_card_number' => $validated['sd_card_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'repository_id' => $validated['repository_id'] ?? null,
            'status' => $validated['status'] ?? 0,
        ];

        $record->update($data);

        return redirect()->to(route('camera.all') . '?tab=returned')->with('message', 'Returned record updated successfully.');
    }
}
