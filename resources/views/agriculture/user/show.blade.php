@extends('layouts/layoutMaster')

@section('title', 'Agriculture User Details')

@section('content')

<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Agriculture Users / </span> View Details
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Agriculture User Details</h5>
                <div>
                    <a href="{{ route('argiculture-user.edit', $agricultureUser->id) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    <a href="{{ route('argiculture-user.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Household</label>
                            <p class="form-control-plaintext">{{ optional($agricultureUser->household)->english_name ?? '—' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Community</label>
                            <p class="form-control-plaintext">{{ optional($agricultureUser->community)->english_name ?? '—' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">{{ optional($agricultureUser->agricultureHolderStatus)->english_name ?? '—' }}</p>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label fw-bold">Requested Date</label>
                                <p class="form-control-plaintext">{{ $agricultureUser->requested_date ? \Carbon\Carbon::parse($agricultureUser->requested_date)->format('Y-m-d') : '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label fw-bold">Confirmation Date</label>
                                <p class="form-control-plaintext">{{ $agricultureUser->confirmation_date ? \Carbon\Carbon::parse($agricultureUser->confirmation_date)->format('Y-m-d') : '—' }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">System Cycle</label>
                            <p class="form-control-plaintext">{{ optional($agricultureUser->agricultureSystemCycle)->name ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type of Installation</label>
                                <p class="form-control-plaintext">
                                    {{ isset($agricultureUser->agriculture_installation_type_id) && $agricultureUser->agriculture_installation_type_id
                                        ? (App\Models\AgricultureInstallationType::find($agricultureUser->agriculture_installation_type_id)->english_name ?? '—')
                                        : '—' }}
                                </p>

                                

                                @if(!empty($contributionBreakdown))
                                    <div class="mt-2">
                                        <strong>Per-system Contribution</strong>
                                        <ul class="mb-0">
                                            @foreach($contributionBreakdown as $b)
                                                <li>
                                                    {{ $b['system_name'] ?? ('#'.$b['system_id']) }} :
                                                    <strong>{{ $b['amount'] !== null ? number_format($b['amount'],0) . '₪' : '—' }}</strong>
                                                    @if(empty($b['system_type'])) <small class="text-muted">(unknown type)</small>@endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Area of Installation</label>
                            <p class="form-control-plaintext">{{ $agricultureUser->area_of_installation ?? '—' }}</p>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label fw-bold">Azolla Units</label>
                                <p class="form-control-plaintext">{{ $agricultureUser->azolla_unit ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label fw-bold">Herd Size</label>
                                <p class="form-control-plaintext">{{ $agricultureUser->size_of_herds ?? 0 }}</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Animals</label>
                            <p class="form-control-plaintext mb-0">
                                Goats: {{ $agricultureUser->size_of_goat ?? 0 }}<br>
                                Cows: {{ $agricultureUser->size_of_cow ?? 0 }}<br>
                                Camels: {{ $agricultureUser->size_of_camel ?? 0 }}<br>
                                Chickens: {{ $agricultureUser->size_of_chicken ?? 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                
                

                {{-- Shared Herd Information --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Shared Herd Information</h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Prefer Eloquent relation if available, otherwise use fallback from controller
                            $sharedRows = [];
                            if (isset($sharedHerds) && $sharedHerds->count() > 0) {
                                $sharedRows = $sharedHerds;
                                $useRaw = true;
                            } elseif ($agricultureUser->relationLoaded('agricultureSharedHolders') && $agricultureUser->agricultureSharedHolders->count() > 0) {
                                $sharedRows = $agricultureUser->agricultureSharedHolders;
                                $useRaw = false;
                            } elseif ($agricultureUser->agricultureSharedHolders && $agricultureUser->agricultureSharedHolders->count() > 0) {
                                $sharedRows = $agricultureUser->agricultureSharedHolders;
                                $useRaw = false;
                            } else {
                                $sharedRows = collect();
                                $useRaw = false;
                            }
                        @endphp

                        @if($sharedRows->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Household</th>
                                            <th>Sheep (size_of_herds)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sharedRows as $shared)
                                            <tr>
                                                @if(!empty($useRaw))
                                                    <td>{{ isset($shared->household_id) ? (App\Models\Household::find($shared->household_id)->english_name ?? '—') : '—' }}</td>
                                                    <td>{{ $shared->size_of_herds ?? 0 }}</td>
                                                @else
                                                    <td>{{ optional($shared->household)->english_name ?? '—' }}</td>
                                                    <td>{{ $shared->size_of_herds ?? 0 }}</td>
                                                    <td>{{ optional($shared->created_at)->format('Y-m-d H:i:s') ?? '—' }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No shared herd records for this holder.</p>
                        @endif
                    </div>
                </div>
                

        
                {{-- Assigned Systems and Components --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Assigned Systems</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $systems = collect();
                            if (isset($assignedSystems) && $assignedSystems instanceof \Illuminate\Support\Collection) {
                                $systems = $assignedSystems;
                            } elseif ($agricultureUser->relationLoaded('agricultureSystems') && $agricultureUser->agricultureSystems->count()>0) {
                                $systems = $agricultureUser->agricultureSystems;
                            } elseif ($agricultureUser->agricultureHolderSystems && $agricultureUser->agricultureHolderSystems->count()>0) {
                                $systems = $agricultureUser->agricultureHolderSystems->map(function($hs){ return optional($hs->agricultureSystem); })->filter();
                            }
                        @endphp

                        @if($systems->count() > 0)
                            @foreach($systems as $sys)
                                <div class="card mt-2">
                                    <div class="card-header">
                                        <strong>{{ optional($sys)->name ?? optional($sys)->english_name ?? 'Unnamed System' }}</strong>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $holderComponents = collect();
                                            if ($agricultureUser->relationLoaded('agricultureSystemComponentHolders') && $agricultureUser->agricultureSystemComponentHolders->count()>0) {
                                                $holderComponents = $agricultureUser->agricultureSystemComponentHolders->filter(function($h) use ($sys) {
                                                    return optional($h->agricultureSystemComponent)->agriculture_system_id == optional($sys)->id;
                                                });
                                            } elseif ($agricultureUser->agricultureSystemComponentHolders && $agricultureUser->agricultureSystemComponentHolders->count()>0) {
                                                $holderComponents = $agricultureUser->agricultureSystemComponentHolders->filter(function($h) use ($sys) {
                                                    return optional($h->agricultureSystemComponent)->agriculture_system_id == optional($sys)->id;
                                                });
                                            }
                                        @endphp

                                        @if($holderComponents->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Component</th>
                                                            <th>Model</th>
                                                            <th>Quantity</th>
                                                            <th>Unit Price</th>
                                                            <th style="width:140px">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($holderComponents as $hc)
                                                            @php
                                                                // component name from related system component if available
                                                                $compName = optional(optional($hc->agricultureSystemComponent)->agricultureComponent)->english_name ?? null;
                                                                // try to get model id from relation first
                                                                $modelId = optional($hc->agricultureSystemComponent)->agriculture_component_model_id ?? null;

                                                                // if model id not available, try to fetch from agriculture_system_components table
                                                                if (!$modelId && $hc->agriculture_system_component_id) {
                                                                    try {
                                                                        $cols = \Schema::getColumnListing('agriculture_system_components');
                                                                    } catch (\Exception $e) {
                                                                        $cols = [];
                                                                    }
                                                                    $modelColCandidate = null;
                                                                    foreach ($cols as $c) {
                                                                        $lc = strtolower($c);
                                                                        if (str_contains($lc, 'model') && str_ends_with($lc, '_id')) { $modelColCandidate = $c; break; }
                                                                    }
                                                                    $modelColCandidate = $modelColCandidate ?? 'agriculture_component_model_id';
                                                                    try {
                                                                        $row = \DB::table('agriculture_system_components')->where('id', $hc->agriculture_system_component_id)->first();
                                                                        if ($row && isset($row->{$modelColCandidate})) {
                                                                            $modelId = $row->{$modelColCandidate};
                                                                        }
                                                                        if (!$compName && $row) {
                                                                            // attempt to get component name
                                                                            $componentColCandidate = null;
                                                                            foreach ($cols as $c2) {
                                                                                $lc2 = strtolower($c2);
                                                                                if (str_contains($lc2, 'component') && str_ends_with($lc2, '_id')) { $componentColCandidate = $c2; break; }
                                                                            }
                                                                            $componentColCandidate = $componentColCandidate ?? 'agriculture_component_id';
                                                                            $compIdFallback = $row->{$componentColCandidate} ?? null;
                                                                            $compName = $compName ?? optional(App\Models\AgricultureComponent::find($compIdFallback))->english_name;
                                                                        }
                                                                    } catch (\Exception $e) {
                                                                        // ignore
                                                                    }
                                                                }

                                                                $modelName = $modelId ? (optional(App\Models\AgricultureComponentModel::find($modelId))->model ?? '—') : '—';
                                                                $compName = $compName ?? '—';
                                                                $qty = $hc->quantity ?? optional($hc->agricultureSystemComponent)->quantity ?? '—';
                                                                $up = $hc->unit_price ?? optional($hc->agricultureSystemComponent)->unit_price ?? '—';
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $compName }}</td>
                                                                <td>{{ $modelName }}</td>
                                                                <td>{{ $qty }}</td>
                                                                <td>{{ $up }}</td>
                                                                <td class="text-end">
                                                                    <a href="{{ route('argiculture-user.systems.components.edit', ['holder' => $agricultureUser->id, 'system' => optional($sys)->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                                    <form method="post" action="{{ route('argiculture-user.systems.components.delete', ['holder' => $agricultureUser->id, 'system' => optional($sys)->id, 'holder_component' => $hc->id]) }}" style="display:inline-block" onsubmit="return confirm('Remove this holder-specific component?');">
                                                                        @csrf
                                                                        <button class="btn btn-sm btn-danger">Remove</button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            @php
                                                try {
                                                    $columns = \Schema::getColumnListing('agriculture_system_components');
                                                } catch (\Exception $e) {
                                                    $columns = [];
                                                }

                                                $systemCol = null; $componentCol = null; $modelCol = null; $quantityCol = null; $unitPriceCol = null; $archivedCol = null;
                                                foreach ($columns as $col) {
                                                    $lc = strtolower($col);
                                                    if (!$systemCol && str_contains($lc, 'system') && str_ends_with($lc, '_id')) $systemCol = $col;
                                                    if (!$componentCol && str_contains($lc, 'component') && str_ends_with($lc, '_id')) $componentCol = $col;
                                                    if (!$modelCol && str_contains($lc, 'model') && str_ends_with($lc, '_id')) $modelCol = $col;
                                                    if (!$quantityCol && ($lc === 'quantity' || str_contains($lc, 'qty'))) $quantityCol = $col;
                                                    if (!$unitPriceCol && (str_contains($lc, 'unit_price') || str_contains($lc, 'unitprice') || str_contains($lc, 'unit'))) $unitPriceCol = $col;
                                                    if (!$archivedCol && ($lc === 'is_archived' || $lc === 'archived')) $archivedCol = $col;
                                                }

                                                $systemCol = $systemCol ?? 'agriculture_system_id';
                                                $componentCol = $componentCol ?? 'agriculture_component_id';
                                                $modelCol = $modelCol ?? 'agriculture_component_model_id';
                                                $quantityCol = $quantityCol ?? 'quantity';
                                                $unitPriceCol = $unitPriceCol ?? 'unit_price';
                                                $archivedCol = $archivedCol ?? 'is_archived';

                                                $query = \DB::table('agriculture_system_components as asc')
                                                    ->where("asc.{$systemCol}", optional($sys)->id)
                                                    ->where("asc.{$archivedCol}", 0);

                                                $systemComponents = collect();
                                                try {
                                                    $systemComponents = $query->get();
                                                } catch (\Exception $e) {
                                                    $systemComponents = collect();
                                                }
                                            @endphp

                                            @if($systemComponents->count() > 0)
                                                <p class="text-muted mb-2 ">Showing system components for <b>{{ optional($agricultureUser->household)->english_name ?? '—' }}.</b></p>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Component</th>
                                                                <th>Model</th>
                                                                <th>Quantity</th>
                                                                <th>Unit Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                // Load holder-specific copies for these system components to prefer their values
                                                                try {
                                                                    $scIds = collect($systemComponents)->pluck('id')->toArray();
                                                                    $holderCopies = \DB::table('agriculture_system_component_holders')
                                                                        ->where('agriculture_holder_id', $agricultureUser->id)
                                                                        ->whereIn('agriculture_system_component_id', $scIds)
                                                                        ->where('is_archived', 0)
                                                                        ->get()
                                                                        ->keyBy('agriculture_system_component_id');
                                                                } catch (\Exception $e) {
                                                                    $holderCopies = collect();
                                                                }
                                                            @endphp
                                                            @foreach($systemComponents as $sc)
                                                                @php
                                                                    $compId = $sc->{$componentCol} ?? ($sc->component_id ?? null);
                                                                    $modelId = $sc->{$modelCol} ?? ($sc->model_id ?? null);
                                                                    $hc = $holderCopies->get($sc->id) ?? null;
                                                                    $qty = $hc ? $hc->quantity : ($sc->{$quantityCol} ?? ($sc->quantity ?? null));
                                                                    $up = $hc ? $hc->unit_price : ($sc->{$unitPriceCol} ?? ($sc->unit_price ?? null));
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ optional(App\Models\AgricultureComponent::find($compId))->english_name ?? '—' }}</td>
                                                                    <td>{{ optional(App\Models\AgricultureComponentModel::find($modelId))->model ?? '—' }}</td>
                                                                    <td>{{ $qty ?? '—' }}</td>
                                                                    <td>{{ $up ?? '—' }}</td>
                                                                    <td class="text-end">
                                                                        <a href="{{ route('argiculture-user.systems.components.edit', ['holder' => $agricultureUser->id, 'system' => optional($sys)->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted">No components defined for this system.</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No systems have been assigned to this holder.</p>
                        @endif
                    </div>
                </div>

                                {{-- Donors Information --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Donors</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $holderDonors = collect();
                            if (isset($donorsList) && $donorsList instanceof \Illuminate\Support\Collection && $donorsList->count()>0) {
                                $holderDonors = $donorsList;
                            } elseif ($agricultureUser->relationLoaded('agricultureHolderDonors') && $agricultureUser->agricultureHolderDonors->count()>0) {
                                $holderDonors = $agricultureUser->agricultureHolderDonors;
                            } elseif ($agricultureUser->agricultureHolderDonors && $agricultureUser->agricultureHolderDonors->count()>0) {
                                $holderDonors = $agricultureUser->agricultureHolderDonors;
                            }
                        @endphp

                        @if($holderDonors->count() > 0)
                            <div class="list-group">
                                @foreach($holderDonors as $hd)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium">{{ optional($hd->donor)->donor_name ?? optional($hd)->donor_name ?? 'Unknown' }}</div>
                                            <small class="text-muted">{{ optional($hd->donor)->email ?? optional($hd)->email ?? '' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">Joined: {{ optional($hd->created_at)->format('Y-m-d') ?? '—' }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No donors are associated with this holder.</p>
                        @endif
                    </div>
                </div> 
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card p-2 border-0 bg-light">
                            <div class="card-body p-3">
                                <h6 class="mb-2 fw-bold">Notes</h6>
                                <p class="mb-0 text-muted">{{ $agricultureUser->notes ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>

@endsection