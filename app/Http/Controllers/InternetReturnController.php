<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternetSystem;
use App\Models\InternetSystemCommunity;
use App\Models\InternetSystemReturn;
use App\Models\InternetSystemReturnItem;
use App\Models\Community;
use App\Models\User;

class InternetReturnController extends Controller
{
    public function create()
    {
        $systems = InternetSystem::all();
        $communities = Community::where('is_archived',0)->where('internet_service','YES')->orderBy('english_name')->get();
        $users = User::where('is_archived',0)->get();
        $reasons = collect([]);

        // preload internet systems for each community from internet_system_communities
        $links = InternetSystemCommunity::with('InternetSystem')
                    ->whereIn('community_id', $communities->pluck('id')->all())
                    ->get();

        $systemsByCommunity = [];
        foreach ($links as $link) {
            if (! $link->InternetSystem) continue;
            $cid = $link->community_id;
            $systemsByCommunity[$cid][] = [
                'id' => $link->InternetSystem->id,
                'label' => $link->InternetSystem->system_name ?? $link->InternetSystem->name ?? ('System ' . $link->InternetSystem->id),
            ];
        }

        return view('system.internet.returns.create', compact('systems','communities','users','reasons','systemsByCommunity'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
    'community_id'=> 'required|exists:communities,id',
        'internet_system_id' => 'required|exists:internet_systems,id',
            'returned_by' => 'required|exists:users,id',
            'return_date' => 'required|date',
            'status' => 'nullable|in:0,1,2,3,4',
            'notes' => 'nullable|string',
            'reason_id' => 'nullable',
            'items' => 'nullable|array'
        ]);

        $data = [
            'community_id' => $validated['community_id'],
            'internet_system_id' => $validated['internet_system_id'],
            'returned_by' => $validated['returned_by'],
            'return_date' => $validated['return_date'],
            'status' => $validated['status'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'reason' => $validated['reason_id'] ?? null,
        ];

        $ret = InternetSystemReturn::create($data);

        if ($request->filled('items') && is_array($request->items)) {
            foreach ($request->items as $it) {
                $itemData = [
                    'internet_system_return_id' => $ret->id,
                    'component_type' => $it['component_type'] ?? null,
                    'component_id' => $it['component_id'] ?? null,
                    'quantity' => $it['quantity'] ?? 1,
                    'notes' => isset($it['component_serial']) ? $it['component_serial'] : ($it['notes'] ?? null),
                ];
                InternetSystemReturnItem::create($itemData);
            }
        }

        return redirect()->back()->with('message', 'Internet system return created successfully.');
    }

    /**
     * API: return components for given type and optional system
     */
    public function components(Request $request)
    {
        $type = $request->query('type');
        $systemId = $request->query('internet_system_id');

        if (! $type) return response()->json([]);

        // try to resolve model class name
        $modelClass = $type;
        $items = [];

        if ($systemId) {
            $system = InternetSystem::find($systemId);
            if ($system) {
                $map = [
                    'Router' => 'routers',
                    'Switche' => 'switches',
                    'InternetPtp' => 'ptps',
                    'InternetUisp' => 'uisps',
                    'InternetAp' => 'aps',
                    'InternetConnector' => 'connectors',
                    'NetworkCabinet' => 'networkCabinets',
                ];

                $base = class_basename($type);
                if (isset($map[$base]) && method_exists($system, $map[$base])) {
                    $collection = $system->{$map[$base]}()->get();
                    $items = $collection->map(function ($m) {
                        return ['id' => $m->id, 'label' => ($m->name ?? $m->model ?? ($m->english_name ?? ('#' . $m->id)))];
                    })->values()->all();
                }
            }
        }

        // fallback: try to instantiate model and return all records
        if (empty($items)) {
            if (class_exists($modelClass)) {
                try {
                    $collection = $modelClass::all();
                    $items = $collection->map(function ($m) {
                        return ['id' => $m->id, 'label' => ($m->name ?? $m->model ?? ($m->english_name ?? ('#' . $m->id)))];
                    })->values()->all();
                } catch (\Throwable $e) {
                    $items = [];
                }
            }
        }

        return response()->json($items);
    }

    /**
     * API: return internet systems for a given community
     */
    public function systems(Request $request)
    {
        $communityId = $request->query('community_id');
        if (! $communityId) return response()->json([]);

        $links = InternetSystemCommunity::with('InternetSystem')
                    ->where('community_id', $communityId)
                    ->get();

        $systems = $links->map(function ($link) {
            $sys = $link->InternetSystem;
            if (! $sys) return null;
            return ['id' => $sys->id, 'label' => $sys->system_name ?? $sys->name ?? ('System ' . $sys->id)];
        })->filter()->values()->all();

        return response()->json($systems);
    }
}
