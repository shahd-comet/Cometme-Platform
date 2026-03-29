@extends('layouts/layoutMaster')

@section('title', 'Meter History')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/meter-history-show.css') }}?v={{ time() }}" />
@endsection

@include('layouts.all')

@section('content')
<div class="content-wrapper">

<div class="container-fluid">
    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    

    <!-- Meter Information Section -->
    <div class="meter-info-section">
        <div class="meter-info-header">
            <h4><i class="bx bx-info-circle me-2 text-primary"></i>Meter Information</h4>
            <div class="action-buttons">

                <div class="btn-group" role="group">
                    <a href="{{ route('meter-history.new') }}?meter_number={{ urlencode($meter_number) }}" class="btn btn-sm btn-outline-success" id="addNewHistoryBtn" title="Add New History Entry">
                        <i class="bx bx-plus"></i> Add History
                    </a>
                    <button class="btn btn-sm btn-outline-info" id="addNewUpdateBtn" title="Add Status Update" data-bs-toggle="modal" data-bs-target="#addUpdateModal">
                        <i class="bx bx-refresh"></i> Add an Update
                    </button>
                </div>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-warning" id="editHistoryBtn" title="Edit History Records" data-bs-toggle="modal" data-bs-target="#editHistoryModal">
                        <i class="bx bx-edit"></i> Edit
                    </button>

                </div>
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Meter Number:</div>
                    <div class="info-value">
                        <a href="#">{{ $meter_number }}</a>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Community:</div>
                    <div class="info-value">
                        <a href="#">{{ $communityName ?? ($latest->community->english_name ?? 'N/A') }}</a>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Meter Status:</div>
                    <div class="info-value">
                        @php
                            $statusName = $latest->status->english_name ?? 'N/A';
                            $isNotUsed = stripos($statusName, 'not used') !== false;
                            $isRelocated = stripos($statusName, 'relocated') !== false;
                            $isShared = stripos($statusName, 'shared') !== false || stripos($statusName, 'become a shared') !== false;
                            
                            // Determine badge color based on status
                            if ($isNotUsed) {
                                $badgeClass = 'bg-danger';     // Red
                            } elseif ($isRelocated) {
                                $badgeClass = 'bg-warning';    // Yellow/Orange
                            } elseif ($isShared) {
                                $badgeClass = 'bg-purple';     // Custom Purple
                            } else {
                                $badgeClass = 'bg-success';    // Green
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusName }}</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Last Updated:</div>
                    <div class="info-value">{{ $latest->date ?? $latest->created_at ?? 'N/A' }}</div>
                </div>
            </div>

            <div>
                <div class="info-item">
                    <div class="info-label">Current Holder:</div>
                    <div class="info-value">{{ $latest->newHousehold->english_name ?? ($latest->household->english_name ?? 'N/A') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Household Status:</div>
                    <div class="info-value">
                        <span class="badge bg-light">{{ $latest->household_status ?? ($latest->new_holder_status ?? 'N/A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Dates Section -->
    @if($meter && (count($purchaseDates) > 0 || $meter->installation_date))
    <div class="purchase-dates-section">
        <h5>
            <i class="bx bx-calendar-check me-2 text-success"></i>Purchase Data & History
        </h5>
        <div class="purchase-grid">
            @if($meter->installation_date)
            <div class="info-item">
                <div class="info-label">Installation Date</div>
                <div class="info-value">{{ $meter->installation_date }}</div>
            </div>
            @endif

            @foreach($purchaseDates as $index => $date)
            <div class="info-item">
                <div class="info-label">Last Purchase Date {{ $index + 1 }}</div>
                <div class="info-value">{{ $date }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- History Timeline Section -->
    <div class="timeline-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">
                <i class="bx bx-time-five me-2 text-primary"></i>History Timeline
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-2">
                    <i class="bx bx-list-ul me-1"></i>{{ $histories->count() }} Events
                </span>
                @if($histories->count() > 0)
                <small class="text-muted">
                    Latest: {{ \Carbon\Carbon::parse($histories->first()->date ?? $histories->first()->created_at)->format('d M Y') }}
                </small>
                @endif
            </div>
        </div>

        

        @if($histories->isEmpty())
        <div class="no-history-message">
            <i class="bx bx-history"></i>
            <h5>No History Available</h5>
            <p>There are no history records for this meter yet.</p>
        </div>
        @else
        <div class="timeline">
            @foreach($histories as $history)
            <div class="timeline-item">
                @php
                    // Check status types
                    $statusName = $history->status->english_name ?? '';
                    $isReplaced = stripos($statusName, 'replaced') !== false;
                    $isRelocated = stripos($statusName, 'relocated') !== false;
                    $isTransfer = stripos($statusName, 'transfer') !== false;
                    $isUsedByOther = stripos($statusName, 'used by other') !== false;
                    $isShared = stripos($statusName, 'shared') !== false;
                    $isCommunityChange = $isRelocated || $isTransfer;
                    
                    // Determine icon and styling based on status
                    if ($isReplaced) {
                        $iconClass = 'replacement';
                        $icon = 'bx-refresh';
                        $eventType = 'Meter Replacement';
                        $badgeText = 'REPLACED';
                    } elseif ($isUsedByOther) {
                        $iconClass = 'transfer';
                        $icon = 'bx-user';
                        $eventType = 'Holder Change';
                        $badgeText = 'TRANSFER';
                    } elseif ($isCommunityChange) {
                        $iconClass = 'transfer';
                        $icon = 'bx-transfer-alt';
                        $eventType = 'Community Transfer';
                        $badgeText = 'RELOCATION';
                    } elseif ($isShared) {
                        $iconClass = 'shared';
                        $icon = 'bx-group';
                        $eventType = 'Became Shared';
                        $badgeText = 'SHARED';
                    } else {
                        $iconClass = 'replacement';
                        $icon = 'bx-edit';
                        $eventType = 'Status Update';
                        $badgeText = 'UPDATE';
                    }
                @endphp

                <div class="timeline-icon {{ $iconClass }}">
                    <i class="bx {{ $icon }}"></i>
                </div>

                <div class="timeline-content">
                    <div class="timeline-header">
                        <div class="timeline-title">
                            {{ $eventType }}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="timeline-badge {{ $iconClass }}">
                                {{ $badgeText }}
                            </span>
                            <div class="btn-group" role="group">

                                <button class="btn btn-sm btn-outline-danger delete-history-btn" 
                                        data-history-id="{{ $history->id }}"
                                        title="Delete this record">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-description">
                        @if($isReplaced)
                            {{-- Meter Replacement Case --}}
                            Meter <strong>{{ $history->old_meter_number ?? $meter_number }}</strong> was replaced with 
                            <strong>{{ $history->new_meter_number ?? 'New Meter' }}</strong> 
                            for holder <strong>{{ $history->household->english_name ?? 'Unknown User' }}</strong>
                            @if($history->new_meter_number)
                                <br><small class="text-muted">Old meter: {{ $history->old_meter_number ?? $meter_number }} → New meter: {{ $history->new_meter_number }}</small>
                            @endif
                        @elseif($isUsedByOther)
                            {{-- Used by Other Case --}}
                            Meter holder changed from 
                            <strong>{{ $history->main_holder_name ?? 'Main Holder Not Found' }}</strong>
                            @if($history->is_public_structure)
                                <span class="badge bg-secondary ms-1">Public Structure</span>
                            @endif
                            to 
                            <strong>{{ $history->new_holder_name ?? 'New Holder Not Found' }}</strong>
                            @if($history->is_new_holder_public_structure)
                                <span class="badge bg-secondary ms-1">Public Structure</span>
                            @endif
                            @if($history->new_meter_number && $history->new_meter_number !== 'no data')
                                <br><small class="text-muted">New holder's previous meter: {{ $history->new_meter_number }}</small>
                            @endif
                        @elseif($isCommunityChange)
                            {{-- Community Change Case --}}
                            @if($isRelocated)
                                <strong>{{ $history->household->english_name ?? $history->newHousehold->english_name ?? 'Unknown User' }}</strong> relocated from 
                                <strong>{{ $history->community->english_name ?? 'Unknown Community' }}</strong> 
                                to 
                                <strong>{{ $history->newCommunity->english_name ?? 'Unknown Community' }}</strong>
                            @else
                                Meter transferred from 
                                <strong>{{ $history->community->english_name ?? 'Unknown Community' }}</strong> 
                                to 
                                <strong>{{ $history->newCommunity->english_name ?? 'Unknown Community' }}</strong>
                            @endif
                        @elseif($isShared)
                            {{-- Became Shared Case --}}
                            Meter became shared with 
                            <strong>{{ $history->shared_user_name ?? 'Unknown User' }}</strong>
                            @if($history->household)
                                <br><small class="text-muted">Main holder: {{ $history->household->english_name }}</small>
                            @endif
                        @else
                            {{-- Default Case --}}
                            Status update for 
                            <strong>{{ $history->household->english_name ?? 'Unknown User' }}</strong>

                        @endif
                    </div>

                    <div class="timeline-meta">
                        <div><strong>Reason:</strong> {{ $history->reason->english_name ?? 'N/A' }}</div>
                        <div><strong>Community:</strong> {{ $history->community->english_name ?? 'N/A' }}
                            @if($isCommunityChange && $history->newCommunity)
                                → {{ $history->newCommunity->english_name }}
                            @endif
                        </div>
                        
                        @if($isReplaced)
                            {{-- Replacement specific meta --}}
                            <div><strong>Main Meter:</strong> {{ $history->old_meter_number ?? $meter_number }}</div>
                            @if($history->new_meter_number)
                            <div><strong>New Meter:</strong> {{ $history->new_meter_number }}</div>
                            @endif
                            <div><strong>Holder:</strong> 
                                {{ $history->main_holder_name ?? 'N/A' }}
                                @if($history->is_public_structure)
                                    <span class="badge bg-secondary ms-1">Public Structure</span>
                                @endif
                            </div>
                        @elseif($isUsedByOther)
                            {{-- Used by Other specific meta --}}
                            <div><strong>Main Holder :</strong> 
                                {{ $history->main_holder_name ?? 'Main Holder Name Not Found' }}
                                @if($history->is_public_structure)
                                    <span class="badge bg-secondary ms-1">Public Structure</span>
                                @endif
                            </div>
                            <div><strong>New Holder :</strong> 
                                {{ $history->new_holder_name ?? 'New Holder Name Not Found' }}
                                @if($history->is_new_holder_public_structure)
                                    <span class="badge bg-secondary ms-1">Public Structure</span>
                                @endif
                            </div>
                            @if($history->new_meter_number && $history->new_meter_number !== 'no data')
                            <div><strong>New Holder's Previous Meter:</strong> {{ $history->new_meter_number }}</div>
                            @endif
                        @elseif($isShared)
                            {{-- Shared specific meta --}}
                            <div><strong>Main Holder:</strong> 
                                {{ $history->main_holder_name ?? 'N/A' }}
                                @if($history->is_public_structure)
                                    <span class="badge bg-secondary ms-1">Public Structure</span>
                                @endif
                            </div>
                            <div><strong>Shared User:</strong> {{ $history->shared_user_name ?? 'N/A' }}</div>
                        @elseif(!$isCommunityChange && $history->newHousehold)
                            {{-- General holder change --}}
                            <div><strong>New Holder:</strong> {{ $history->newHousehold->english_name }}</div>
                        @endif
                        
                        @if($history->updated_by_user_id)
                        <div><strong>Updated By:</strong> System Admin</div>
                        @endif
                        @if($history->notes)
                        <div><strong>Notes:</strong> {{ $history->notes }}</div>
                        @endif
                    </div>

                    <div class="timeline-date">
                        {{ \Carbon\Carbon::parse($history->date ?? $history->created_at)->format('d-M-Y') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Add Update Modal -->
<div class="modal fade" id="addUpdateModal" tabindex="-1" aria-labelledby="addUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUpdateModalLabel">Add Status Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUpdateForm" method="POST" action="{{ route('meter-history.add-update') }}">
                @csrf
                <input type="hidden" name="meter_number" value="{{ $meter_number }}">
                <input type="hidden" name="current_household_id" value="{{ $latest->household_id ?? '' }}">
                <input type="hidden" name="current_community_id" value="{{ $latest->community_id ?? '' }}">
                
                <div class="modal-body">
                    <!-- Current Meter Information -->
                    <div class="alert alert-info">
                        <h6><i class="bx bx-info-circle me-2"></i>Current Meter Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Meter Number:</strong> {{ $meter_number }}<br>
                                <strong>Current Holder:</strong> {{ $latest->newHousehold->english_name ?? ($latest->household->english_name ?? 'N/A') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Community:</strong> {{ $communityName ?? ($latest->community->english_name ?? 'N/A') }}<br>
                                <strong>Current Status:</strong> 
                                <span class="badge {{ $badgeClass ?? 'bg-secondary' }}">{{ $latest->status->english_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="update_date" class="form-label">Update Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="update_date" name="date" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="update_status_id" class="form-label">New Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="update_status_id" name="meter_history_status_id" required>
                                    <option value="">Select New Status</option>
                                    @foreach(\App\Models\MeterHistoryStatuses::all() as $status)
                                        <option value="{{ $status->id }}" data-status-name="{{ strtolower($status->english_name) }}">
                                            {{ $status->english_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Select the new status for this meter</small>
                            </div>

                            <div class="mb-3">
                                <label for="update_reason_id" class="form-label">Reason</label>
                                <select class="form-select" id="update_reason_id" name="meter_history_reason_id">
                                    <option value="">Select Reason</option>
                                    @foreach(\App\Models\MeterHistoryReason::all() as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->english_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="update_notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="update_notes" name="notes" rows="4" placeholder="Additional notes about this update..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Replacement Fields -->
                    <div id="updateReplacementFieldsDiv" class="card border-primary mt-4" style="display: none;"> 
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bx bx-refresh me-2 text-primary"></i>Replacement Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>New Meter Number <span class="text-danger">*</span></label>
                                        <input type="text" name="new_meter_number" class="form-control" 
                                               placeholder="Enter new meter number" id="updateNewMeterNumberInput">
                                        <div class="form-text">The meter number that will replace the current one</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>Community <small class="text-muted">(optional)</small></label>
                                        <select class="form-select" name="community_id" id="updateCommunitySelected">
                                            <option value="">Choose one...</option>
                                            @foreach(\App\Models\Community::orderBy('english_name')->get() as $community)
                                                <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Community where the replacement meter is located</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Used By Other Fields -->
                    <div id="updateUsedByOtherFieldsDiv" class="card border-info mt-4" style="display: none;"> 
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bx bx-user-plus me-2 text-info"></i>Transfer to New Holder
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>New Community <span class="text-danger">*</span></label>
                                        <select class="form-select" name="new_community_id" id="updateNewCommunitySelected">
                                            <option value="">Choose one...</option>
                                            @foreach(\App\Models\Community::orderBy('english_name')->get() as $community)
                                                <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Community where the new holder is located</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>New User Name <span class="text-danger">*</span></label>
                                        <select class="form-select" name="new_household_id" id="updateNewHouseholdSelected">
                                            <option value="">Choose community first...</option>
                                        </select>
                                        <div class="form-text">Select community first to load households</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>New Holder Status</label>
                                        <input type="text" name="new_holder_status" class="form-control" 
                                               placeholder="e.g., Main User" id="updateNewHolderStatusInput">
                                        <div class="form-text">Status of the new holder</div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>Previous Meter Number</label>
                                        <input type="text" name="previous_meter_number" class="form-control" 
                                               placeholder="New holder's previous meter number">
                                        <div class="form-text">Previous meter number of the new holder (if any)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Shared Fields -->
                    <div id="updateSharedFieldsDiv" class="card border-success mt-4" style="display: none;"> 
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bx bx-group me-2 text-success"></i>Shared Meter Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>Shared User Name <span class="text-danger">*</span></label>
                                        <select class="form-select" name="shared_user_id" id="updateSharedUserSelected">
                                            <option value="">Select household from current community...</option>
                                            @foreach(\App\Models\Household::orderBy('english_name')->get() as $household)
                                                <option value="{{ $household->id }}">{{ $household->english_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Household that will share this meter</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Relocated Fields -->
                    <div id="updateRelocatedFieldsDiv" class="card border-warning mt-4" style="display: none;"> 
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bx bx-map me-2 text-warning"></i>Relocation Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class='form-label'>New Community <span class="text-danger">*</span></label>
                                        <select class="form-select" name="relocated_community_id" id="updateRelocatedCommunitySelected">
                                            <option value="">Choose one...</option>
                                            @foreach(\App\Models\Community::orderBy('english_name')->get() as $community)
                                                <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Community where the meter has been relocated to</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Add Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit History Modal - Enhanced GUI -->
<div class="modal fade" id="editHistoryModal" tabindex="-1" aria-labelledby="editHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="bx bx-edit-alt fs-4 me-2 text-primary"></i>
                    <div>
                        <h5 class="modal-title mb-0" id="editHistoryModalLabel">Edit History Record</h5>
                        <small class="text-muted">Modify meter history information</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editHistoryForm" method="POST" action="{{ isset($latest) ? route('meter-history.update', $latest->id) : '#' }}">
                @csrf
                @method('PUT')
                
                <div class="modal-body p-4">
                    <!-- Current Meter Information Banner -->
                    <div class="alert alert-light border mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-info-circle fs-4 text-primary me-3 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2">Current Meter Information</h6>
                                <div class="row text-sm">
                                    <div class="col-md-4">
                                        <strong>Meter Number:</strong><br>
                                        <span class="badge bg-dark">{{ $meter_number }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Current Status:</strong><br>
                                        <span class="badge {{ $badgeClass ?? 'bg-secondary' }}">{{ $latest->status->english_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Current Holder:</strong><br>
                                        <span class="text-dark">{{ $latest->newHousehold->english_name ?? ($latest->household->english_name ?? 'N/A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Sections with Cards -->
                    <div class="row g-4">
                        <!-- Basic Information Card -->
                        <div class="col-lg-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bx bx-calendar me-2 text-primary"></i>Basic Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_date" class="form-label fw-semibold">
                                            <i class="bx bx-calendar-event me-1 text-primary"></i>Date
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control form-control-lg border-2" 
                                               id="edit_date" name="date" 
                                               value="{{ $latest->date ?? now()->format('Y-m-d') }}" required>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Date when this history event occurred
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_status_id" class="form-label fw-semibold">
                                            <i class="bx bx-tag me-1 text-primary"></i>Meter Status
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg border-2" id="edit_status_id" name="meter_history_status_id" required>
                                            <option value="">Choose Status...</option>
                                            @foreach(\App\Models\MeterHistoryStatuses::all() as $status)
                                                <option value="{{ $status->id }}" 
                                                        data-status-type="{{ strtolower(str_replace(' ', '-', $status->english_name)) }}"
                                                        {{ ($currentStatus && $currentStatus->id == $status->id) ? 'selected' : '' }}>
                                                    {{ $status->english_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Current status of the meter
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_reason_id" class="form-label fw-semibold">
                                            <i class="bx bx-comment-detail me-1 text-primary"></i>Reason
                                        </label>
                                        <select class="form-select form-select-lg border-2" id="edit_reason_id" name="meter_history_reason_id">
                                            <option value="">Select Reason...</option>
                                            @foreach(\App\Models\MeterHistoryReason::all() as $reason)
                                                <option value="{{ $reason->id }}" 
                                                    {{ ($currentReasonId && $currentReasonId == $reason->id) ? 'selected' : '' }}>
                                                    {{ $reason->english_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Why this change occurred
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label for="edit_community_id" class="form-label fw-semibold">
                                            <i class="bx bx-buildings me-1 text-primary"></i>Community
                                        </label>
                                        <select class="form-select form-select-lg border-2" id="edit_community_id" name="community_id">
                                            <option value="">Select Community...</option>
                                            @foreach(\App\Models\Community::orderBy('english_name')->get() as $community)
                                                <option value="{{ $community->id }}" 
                                                        {{ ($communityName && $communityName == $community->english_name) ? 'selected' : '' }}>
                                                    {{ $community->english_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Community where the meter is located
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Holder Information Card -->
                        <div class="col-lg-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bx bx-user me-2 text-primary"></i>Holder Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="edit_household_id" class="form-label fw-semibold">
                                            <i class="bx bx-home me-1 text-primary"></i>Main Household
                                        </label>
                                        <select class="form-select form-select-lg border-2" id="edit_household_id" name="household_id">
                                            <option value="">Select Household...</option>
                                            @foreach(\App\Models\Household::orderBy('english_name')->get() as $household)
                                                <option value="{{ $household->id }}" 
                                                        {{ ($currentHousehold && $currentHousehold->id == $household->id) ? 'selected' : '' }}>
                                                    {{ $household->english_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Primary holder of this meter
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_household_status" class="form-label fw-semibold">
                                            <i class="bx bx-shield me-1 text-primary"></i>Household Status
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text border-2">
                                                <i class="bx bx-badge"></i>
                                            </span>
                                            <input type="text" class="form-control border-2" 
                                                   id="edit_household_status" name="household_status" 
                                                   value="{{ $latest->household_status ?? '' }}"
                                                   placeholder="e.g., Main User, Shared User">
                                        </div>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Status of the household holder
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label for="edit_old_meter_number" class="form-label fw-semibold">
                                            <i class="bx bx-hash me-1 text-primary"></i>Old Meter Number
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text border-2">
                                                <i class="bx bx-barcode"></i>
                                            </span>
                                            <input type="text" class="form-control border-2" 
                                                   id="edit_old_meter_number" name="old_meter_number" 
                                                   value="{{ $latest->old_meter_number ?? $meter_number }}"
                                                   placeholder="Previous meter number">
                                        </div>
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>Previous meter number (if replaced)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bx bx-note me-2 text-primary"></i>Additional Notes
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <label for="edit_notes" class="form-label fw-semibold">
                                        <i class="bx bx-message-detail me-1 text-primary"></i>Notes & Comments
                                    </label>
                                    <textarea class="form-control form-control-lg border-2" 
                                              id="edit_notes" name="notes" rows="4"
                                              placeholder="Add any additional notes, comments, or observations about this history record...">{{ $latest->notes ?? '' }}</textarea>
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>Optional: Add detailed information about this meter history event
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div class="d-flex align-items-center text-muted">
                            <i class="bx bx-info-circle me-2"></i>
                            <small>All changes will be saved to the history record</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Update History
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Simplified Edit History Modal Styles */
.modal-xl {
    max-width: 1200px;
}

#editHistoryModal .form-control-lg,
#editHistoryModal .form-select-lg {
    padding: 0.75rem 1rem;
}

#editHistoryModal .border-2 {
    border-width: 2px !important;
}

#editHistoryModal .input-group-text {
    background: #f8f9fa;
    border-right: none;
}

#editHistoryModal .input-group .form-control {
    border-left: none;
}

#editHistoryModal .badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

#editHistoryModal .form-text {
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

/* Loading animation for buttons */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.bx-spin {
    animation: spin 1s linear infinite;
}

/* Dynamic section animations */
.dynamic-section {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<!-- /.content-wrapper -->
</div>

@endsection

@section('page-script')
<script>
// Global utility functions
window.showUpdateSection = function(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
    }
};

window.hideAllUpdateDynamicSections = function() {
    const sections = ['updateReplacementFieldsDiv', 'updateUsedByOtherFieldsDiv', 'updateSharedFieldsDiv', 'updateRelocatedFieldsDiv'];
    sections.forEach(id => {
        const section = document.getElementById(id);
        if (section) {
            section.style.display = 'none';
        }
    });
};

// Status change handler for update modal
window.handleUpdateStatusChange = function() {
    const selectedText = this.options[this.selectedIndex]?.text || '';
    
    // Hide all sections first
    window.hideAllUpdateDynamicSections();
    
    // Show appropriate section based on status
    if (selectedText === 'Replaced') {
        window.showUpdateSection('updateReplacementFieldsDiv');
    } else if (selectedText === 'Used By Other') {
        window.showUpdateSection('updateUsedByOtherFieldsDiv');
    } else if (selectedText === 'Become A Shared') {
        window.showUpdateSection('updateSharedFieldsDiv');
    } else if (selectedText === 'Relocated') {
        window.showUpdateSection('updateRelocatedFieldsDiv');
    }
};

// Setup update modal functionality
$('#addUpdateModal').on('shown.bs.modal', function() {
    const updateStatusElement = document.getElementById('update_status_id');
    
    if (updateStatusElement) {
        updateStatusElement.removeEventListener('change', window.handleUpdateStatusChange);
        updateStatusElement.addEventListener('change', window.handleUpdateStatusChange);
        
        if (updateStatusElement.value && updateStatusElement.selectedIndex > 0) {
            window.handleUpdateStatusChange.call(updateStatusElement);
        }
    }
});

$(document).ready(function() {
    // Initialize status select for update modal
    const updateStatusSelect = document.getElementById('update_status_id');
    if (updateStatusSelect) {
        updateStatusSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex]?.text || '';
            
            // Hide all sections first
            window.hideAllUpdateDynamicSections();
            
            // Reset required attributes
            const fields = {
                updateNewMeterInput: document.getElementById('updateNewMeterNumberInput'),
                updateNewHouseholdSelect: document.getElementById('updateNewHouseholdSelected'),
                updateNewCommunitySelect: document.getElementById('updateNewCommunitySelected'),
                updateSharedUserSelect: document.getElementById('updateSharedUserSelected'),
                updateRelocatedCommunitySelect: document.getElementById('updateRelocatedCommunitySelected')
            };
            
            // Remove required attributes from all dynamic fields
            Object.values(fields).forEach(field => {
                if (field) field.removeAttribute('required');
            });
            
            // Show appropriate section and set required fields
            if (selectedText === 'Replaced') {
                window.showUpdateSection('updateReplacementFieldsDiv');
                if (fields.updateNewMeterInput) {
                    fields.updateNewMeterInput.setAttribute('required', 'required');
                }
            } else if (selectedText === 'Used By Other') {
                window.showUpdateSection('updateUsedByOtherFieldsDiv');
                if (fields.updateNewHouseholdSelect) fields.updateNewHouseholdSelect.setAttribute('required', 'required');
                if (fields.updateNewCommunitySelect) fields.updateNewCommunitySelect.setAttribute('required', 'required');
            } else if (selectedText === 'Become A Shared') {
                window.showUpdateSection('updateSharedFieldsDiv');
                if (fields.updateSharedUserSelect) fields.updateSharedUserSelect.setAttribute('required', 'required');
            } else if (selectedText === 'Relocated') {
                window.showUpdateSection('updateRelocatedFieldsDiv');
                if (fields.updateRelocatedCommunitySelect) fields.updateRelocatedCommunitySelect.setAttribute('required', 'required');
            }
        });
        
        // Trigger initial check if something is already selected
        if (updateStatusSelect.value && updateStatusSelect.selectedIndex > 0) {
            updateStatusSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // Community change handler for "Used By Other" section
    const updateNewCommunitySelect = document.getElementById('updateNewCommunitySelected');
    const updateNewHouseholdSelect = document.getElementById('updateNewHouseholdSelected');
    
    if (updateNewCommunitySelect && updateNewHouseholdSelect) {
        updateNewCommunitySelect.addEventListener('change', function() {
            const communityId = this.value;
            updateNewHouseholdSelect.innerHTML = '<option value="">Loading households...</option>';
            
            if (communityId) {
                fetch(`/api/community/${communityId}/households`)
                    .then(response => response.json())
                    .then(data => {
                        updateNewHouseholdSelect.innerHTML = '<option value="">Choose household...</option>';
                        data.forEach(household => {
                            const option = document.createElement('option');
                            option.value = household.id;
                            option.textContent = household.english_name;
                            updateNewHouseholdSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Failed to load households:', error);
                        updateNewHouseholdSelect.innerHTML = '<option value="">Error loading households</option>';
                    });
            } else {
                updateNewHouseholdSelect.innerHTML = '<option value="">Choose community first...</option>';
            }
        });
    }



    // Utility functions for notifications and validation
    function showSuccessMessage(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                <i class="bx bx-check-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => $('.alert-success').fadeOut(), 5000);
    }

    function showErrorMessage(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                <i class="bx bx-error-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => $('.alert-danger').fadeOut(), 8000);
    }

    function clearValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

    function showValidationErrors(errors, prefix = '') {
        clearValidationErrors();
        Object.keys(errors).forEach(function(field) {
            const selectors = [`[name="${field}"]`, `[name="${prefix}${field}"]`];
            let fieldElement = null;
            
            for (const sel of selectors) {
                const el = $(sel);
                if (el.length) { 
                    fieldElement = el; 
                    break; 
                }
            }
            
            if (fieldElement && fieldElement.length) {
                fieldElement.addClass('is-invalid');
                fieldElement.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
            }
        });
    }

    // Form management functions
    function clearEditForm() {
        $('#editHistoryForm')[0]?.reset();
        $('#editHistoryModalLabel').text('Edit History Record');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        const householdSelects = ['#edit_household_id', '#edit_new_household_id', '#edit_shared_user_id'];
        householdSelects.forEach(selector => {
            const el = $(selector);
            if (el.length) el.html('<option value="">Select Household</option>');
        });
    }

    function resetUpdateForm() {
        $('#addUpdateForm')[0].reset();
        $('#update_date').val(new Date().toISOString().split('T')[0]);
        window.hideAllUpdateDynamicSections();
        clearValidationErrors();
    }

    // Edit history modal functionality
    $('#editHistoryModal').on('hidden.bs.modal', function() {
        clearEditForm();
    });

    $(document).on('click', '.edit-history-btn', function() {
        const historyId = $(this).data('history-id');
        if (historyId) loadEditHistoryModal(historyId);
    });

    $('#editHistoryBtn').on('click', function() {
        $('#editHistoryModalLabel').text('Edit Current Meter History');
        $('#editHistoryModal').modal('show');
    });

    function loadEditHistoryModal(historyId) {
        $('#editHistoryModal').modal('show');
        const submitBtn = $('#editHistoryForm button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Loading...');

        $.ajax({
            url: `/meter-history/${historyId}/edit`,
            method: 'GET',
            success: function(history) {
                populateEditForm(history);
                submitBtn.prop('disabled', false).text(originalText);
                $('#edit_status_id').trigger('change');
            },
            error: function(xhr) {
                showErrorMessage('Failed to load history record for editing: ' + (xhr.responseJSON?.message || xhr.statusText));
                $('#editHistoryModal').modal('hide');
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    }

    function populateEditForm(history) {
        clearEditForm();
        
        const statusName = history.status ? history.status.english_name : 'Unknown Status';
        const recordDate = history.date || 'Unknown Date';
        $('#editHistoryModalLabel').text(`Edit History Record - ${statusName} (${recordDate})`);
        $('#editHistoryForm').attr('action', `/meter-history/${history.id}`);

        // Set form values
        const fields = {
            '#edit_date': history.date,
            '#edit_status_id': history.meter_history_status_id,
            '#edit_reason_id': history.meter_history_reason_id,
            '#edit_community_id': history.community_id,
            '#edit_household_status': history.household_status,
            '#edit_new_holder_status': history.new_holder_status,
            '#edit_old_meter_number': history.old_meter_number,
            '#edit_new_meter_number': history.new_meter_number,
            '#edit_new_community_id': history.new_community_id,
            '#edit_notes': history.notes
        };

        Object.entries(fields).forEach(([selector, value]) => {
            const element = $(selector);
            if (element.length) {
                element.val(value ?? '');
            }
        });

        // Handle household dropdowns
        if (history.community_id) {
            updateHouseholdsForCommunity(history.community_id, function() {
                $('#edit_household_id').val(history.household_id);
                $('#edit_new_household_id').val(history.new_household_id);
                $('#edit_shared_user_id').val(history.shared_user_id);
            });
        } else {
            $('#edit_household_id').val(history.household_id);
            $('#edit_new_household_id').val(history.new_household_id);
            $('#edit_shared_user_id').val(history.shared_user_id);
        }
    }

    // Households loading for edit form
    $('#edit_community_id').on('change', function() {
        const communityId = $(this).val();
        updateHouseholdsForCommunity(communityId);
    });

    function updateHouseholdsForCommunity(communityId, callback) {
        const householdSelects = ['#edit_household_id', '#edit_new_household_id', '#edit_shared_user_id'];
        
        householdSelects.forEach(selector => {
            const el = $(selector);
            if (el.length) el.html('<option value="">Select Household</option>');
        });

        if (communityId) {
            $.get(`/api/community/${communityId}/households`)
                .done(function(households) {
                    households.forEach(function(household) {
                        const option = `<option value="${household.id}">${household.english_name}</option>`;
                        householdSelects.forEach(selector => {
                            const el = $(selector);
                            if (el.length) el.append(option);
                        });
                    });
                    if (callback) callback();
                })
                .fail(function(xhr) {
                    showErrorMessage('Failed to load households for the selected community');
                    if (callback) callback();
                });
        } else {
            if (callback) callback();
        }
    }

    // Edit form submission
    $('#editHistoryForm').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Updating...');
        return true;
    });

    // Delete history record
    $(document).on('click', '.delete-history-btn', function() {
        const historyId = $(this).data('history-id');
        if (!historyId) return;
        if (!confirm('Are you sure you want to delete this history record? This action cannot be undone.')) return;

        $.ajax({
            url: `/meter-history/${historyId}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success) {
                    showSuccessMessage(response.message || 'Deleted successfully');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showErrorMessage(response.message || 'Failed to delete history record');
                }
            },
            error: function() {
                showErrorMessage('An error occurred while deleting the history record');
            }
        });
    });

    // Global search functionality
    function searchMeter() {
        const meterNumber = $('#globalMeterSearch').val().trim();
        if (!meterNumber) { 
            alert('Please enter a meter number'); 
            return; 
        }
        window.location.href = `/meter-history/show/${encodeURIComponent(meterNumber)}`;
    }
    
    window.searchMeter = searchMeter;
    
    $('#globalMeterSearch').on('keypress', function(e) {
        if (e.which === 13) searchMeter();
    });

    // Add Update Button handler
    $('#addNewUpdateBtn').click(function() {
        $('#addUpdateModal').modal('show');
        resetUpdateForm();
        
        setTimeout(function() {
            const statusSelect = document.getElementById('update_status_id');
            if (statusSelect && statusSelect.value && statusSelect.selectedIndex > 0) {
                statusSelect.dispatchEvent(new Event('change'));
            }
        }, 300);
    });

    // Add Update form submission with validation
    $('#addUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        clearValidationErrors();
        
        const statusSelect = $('#update_status_id');
        const selectedText = statusSelect.find('option:selected').text();
        
        // Validate status selection
        if (!statusSelect.val()) {
            statusSelect.addClass('is-invalid');
            showErrorMessage('Please select a status.');
            isValid = false;
        }
        
        // Status-specific validation
        const statusLower = selectedText.toLowerCase();
        
        if (statusLower.includes('replaced')) {
            const newMeterInput = $('#updateNewMeterNumberInput');
            if (!newMeterInput.val().trim()) {
                newMeterInput.addClass('is-invalid');
                showErrorMessage('New meter number is required for replacement status.');
                if (isValid) newMeterInput.focus();
                isValid = false;
            }
        } else if (statusLower.includes('used by other')) {
            const newCommunitySelect = $('#updateNewCommunitySelected');
            const newHouseholdSelect = $('#updateNewHouseholdSelected');
            
            if (!newCommunitySelect.val()) {
                newCommunitySelect.addClass('is-invalid');
                showErrorMessage('New community is required when meter is used by other.');
                if (isValid) newCommunitySelect.focus();
                isValid = false;
            }
            
            if (!newHouseholdSelect.val()) {
                newHouseholdSelect.addClass('is-invalid');
                showErrorMessage('New holder is required when meter is used by other.');
                if (isValid) newHouseholdSelect.focus();
                isValid = false;
            }
        } else if (statusLower.includes('shared') || statusLower.includes('become a shared')) {
            const sharedUserSelect = $('#updateSharedUserSelected');
            if (!sharedUserSelect.val()) {
                sharedUserSelect.addClass('is-invalid');
                showErrorMessage('Shared user is required for shared meter status.');
                if (isValid) sharedUserSelect.focus();
                isValid = false;
            }
        } else if (statusLower.includes('relocated')) {
            const relocatedCommunitySelect = $('#updateRelocatedCommunitySelected');
            if (!relocatedCommunitySelect.val()) {
                relocatedCommunitySelect.addClass('is-invalid');
                showErrorMessage('New community is required for relocation status.');
                if (isValid) relocatedCommunitySelect.focus();
                isValid = false;
            }
        }
        
        if (!isValid) {
            return false;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Adding Update...');
        clearValidationErrors();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showSuccessMessage(response.message || 'Update added successfully!');
                    $('#addUpdateModal').modal('hide');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showErrorMessage(response.message || 'Failed to add update');
                    submitBtn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        showValidationErrors(errors);
                        showErrorMessage('Please correct the validation errors and try again.');
                    } else {
                        showErrorMessage('Validation failed. Please check your input.');
                    }
                } else {
                    showErrorMessage('An error occurred while adding the update: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
                submitBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Reset form when modal is closed
    $('#addUpdateModal').on('hidden.bs.modal', function() {
        resetUpdateForm();
    });
});
</script>

@endsection 