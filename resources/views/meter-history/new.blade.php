@extends('layouts/layoutMaster')

@section('title', 'Add New History')

@include('layouts.all')

@section('content')
<style>
.form-group {
    margin-bottom: 1rem;
}
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
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
.form-label {
    font-weight: 600;
    color: #5a5c69;
}
.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.selectpicker {
    border-radius: 6px;
}
.form-control {
    border-radius: 6px;
    border: 1px solid #d1d3e2;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}
.form-control:focus {
    border-color: #5a67d8;
    box-shadow: 0 0 0 3px rgba(90, 103, 216, 0.1);
}
.text-danger {
    color: #e74c3c !important;
}
.text-muted {
    color: #6c757d !important;
}
.is-invalid {
    border-color: #e74c3c !important;
}
.is-valid {
    border-color: #27ae60 !important;
}
.bootstrap-select.is-invalid .dropdown-toggle {
    border-color: #e74c3c !important;
}

.bootstrap-select.is-valid .dropdown-toggle {
    border-color: #27ae60 !important;
}
.auto-filled {
    animation: highlightAutoFill 2s ease-out;
    border-color: #17a2b8 !important;
}
@keyframes highlightAutoFill {
    0% { 
        background-color: #d1ecf1; 
        border-color: #17a2b8; 
    }
    100% { 
        background-color: transparent; 
        border-color: #17a2b8; 
    }
}
.bootstrap-select.auto-filled .dropdown-toggle {
    animation: highlightAutoFill 2s ease-out;
    border-color: #17a2b8 !important;
    background-color: #d1ecf1;
}
.bootstrap-select.auto-filled .dropdown-toggle:after {
    animation: highlightAutoFill 2s ease-out;
}
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Add New History</h4>
                    <p class="text-muted mb-0">Create a new meter history record with ownership and status changes</p>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                        <i class="bx bx-refresh me-1"></i>Reset Form
                    </button>
                    <button type="submit" form="newMeterHistoryForm" class="btn btn-dark" id="saveBtn">
                        <i class="bx bx-save me-1"></i>Save New History
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="row">
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="row">
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session()->get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="newMeterHistoryForm" method="POST" action="{{ route('meter-history.add-update') }}">
                        @csrf

                        <div class="row">
                            <h6>General Details</h6> 
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                <fieldset class="form-group">
                                    <label class='form-label'>Status <span class="text-danger">*</span></label>
                                    <select class="selectpicker form-control" name="meter_history_status_id" 
                                        data-live-search="true" id="meterStatusSelected" required>
                                        <option >Choose one...</option>
                                        @foreach(\App\Models\MeterHistoryStatuses::orderBy('english_name')->get() as $status)
                                            <option value="{{ $status->id }}">{{ $status->english_name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <div id="meter_history_status_id_error" class="text-danger small"></div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                <fieldset class="form-group">
                                    <label class='form-label'>Reason <small class="text-muted">(optional)</small></label>
                                    <select name="meter_history_reason_id" class="selectpicker form-control" 
                                        id="meterReasonSelected">
                                        <option value="">Choose one...</option>
                                        @foreach(\App\Models\MeterHistoryReason::orderBy('english_name')->get() as $reason)
                                            <option value="{{ $reason->id }}">{{ $reason->english_name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <div id="meter_history_reason_id_error" class="text-danger small"></div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6 mb-3">
                                <fieldset class="form-group">
                                    <label class='form-label'>Date Of Change <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" 
                                           value="{{ now()->format('Y-m-d') }}" 
                                           max="{{ now()->format('Y-m-d') }}" required>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Current Holder Information Section -->
                        <div class="row mt-4">
                            <h6>Current Holder Information</h6>
                        </div>
                        
                        <!-- Holder Type Selection -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <fieldset class="form-group">
                                    <label class='form-label'>Holder Type <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="holder_type" 
                                                   id="holderTypeHousehold" value="household" checked>
                                            <label class="form-check-label" for="holderTypeHousehold">
                                                <i class="bx bx-home me-1"></i>Household
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="holder_type" 
                                                   id="holderTypePublic" value="public">
                                            <label class="form-check-label" for="holderTypePublic">
                                                <i class="bx bx-building me-1"></i>Public Structure
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                                <div id="holder_type_error" class="text-danger small"></div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <fieldset class="form-group">
                                    <label class='form-label'>Community <span class="text-danger">*</span></label>
                                    <select class="selectpicker form-control" name="current_holder_community_id" 
                                        data-live-search="true" required>
                                        <option disabled selected>Choose one...</option>
                                        @foreach($communities as $community)
                                            <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <div id="current_holder_community_id_error" class="text-danger small"></div>
                            </div>
                            <!-- Household Selection (shown when household is selected) -->
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3" id="householdSelectionDiv">
                                <fieldset class="form-group">
                                    <label class='form-label'>Household Name <span class="text-danger">*</span></label>
                                    <select class="selectpicker form-control" name="current_holder_household_id" 
                                        data-live-search="true" id="currentHolderHouseholdSelected" required>
                                        <option disabled selected>Choose community first...</option>
                                    </select>
                                    <div class="form-text">Select community first to load households</div>
                                </fieldset>
                                <div id="current_holder_household_id_error" class="text-danger small"></div>
                            </div>
                            
                            <!-- Public Structure Selection (shown when public structure is selected) -->
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3" id="publicStructureSelectionDiv" style="display: none;">
                                <fieldset class="form-group">
                                    <label class='form-label'>Public Structure <span class="text-danger">*</span></label>
                                    <select class="selectpicker form-control" name="current_holder_public_structure_id" 
                                        data-live-search="true" id="currentHolderPublicStructureSelected">
                                        <option disabled selected>Choose community first...</option>
                                    </select>
                                    <div class="form-text">Select community first to load public structures</div>
                                </fieldset>
                                <div id="current_holder_public_structure_id_error" class="text-danger small"></div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <fieldset class="form-group">
                                    <label class='form-label'>Meter Number <span class="text-danger">*</span></label>
                                    <input type="text" name="current_meter_number" class="form-control" 
                                           placeholder="Select household first..." required>
                                    <!-- <div class="form-text">
                                        <small class="text-muted">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Auto-filled when household is selected
                                        </small>
                                    </div> -->
                                </fieldset>
                                <div id="current_meter_number_error" class="text-danger small"></div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                                <fieldset class="form-group">
                                    <label class='form-label'>Household Status <span class="text-danger">*</span></label>
                                    <select class="selectpicker form-control" name="current_holder_household_status_id" 
                                        data-live-search="true" required>
                                        <option disabled selected>Choose one...</option>
                                        @foreach(\App\Models\HouseholdStatus::where('is_archived', 0)->orderBy('status')->get() as $status)
                                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                                <div id="current_holder_household_status_id_error" class="text-danger small"></div>
                            </div>
                        </div>

                        <!-- Dynamic Replacement Fields -->
                        <div id="replacementFieldsDiv" class="card border-primary mt-4" style="display: none;"> 
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bx bx-refresh me-2 text-primary"></i>Replacement Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class='form-label'>New Meter Number <span class="text-danger">*</span></label>
                                            <input type="text" name="new_meter_number" class="form-control" 
                                                   placeholder="Enter new meter number" id="newMeterNumberInput">
                                            <div class="form-text">The meter number that will replace the current one</div>
                                        </div>
                                        <div id="new_meter_number_error" class="text-danger small"></div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class='form-label'>Community <small class="text-muted">(optional)</small></label>
                                            <select class="selectpicker form-control" name="community_id" 
                                                data-live-search="true" id="communitySelected">
                                                <option value="">Choose one...</option>
                                                @foreach($communities as $community)
                                                    <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Community where the replacement meter is located</div>
                                        </div>
                                        <div id="community_id_error" class="text-danger small"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

        <!-- Dynamic Used By Other Fields -->
        <div id="usedByOtherFieldsDiv" class="card border-info mt-4" style="display: none;"> 
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bx bx-user-plus me-2 text-info"></i>Transfer to New Holder
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                        <div class="form-group">
                            <label class='form-label'>New Community <span class="text-danger">*</span></label>
                            <select class="selectpicker form-control" name="new_holder_community_id" 
                                data-live-search="true" id="newHolderCommunitySelected">
                                <option disabled selected>Choose one...</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Community where the new holder is located</div>
                        </div>
                        <div id="new_holder_community_id_error" class="text-danger small"></div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                        <div class="form-group">
                            <label class='form-label'>New User Name <span class="text-danger">*</span></label>
                            <select class="selectpicker form-control" name="new_user_household_id" 
                                data-live-search="true" id="newUserHouseholdSelected">
                                <option disabled selected>Choose community first...</option>
                            </select>
                            <div class="form-text">Select community first to load households</div>
                        </div>
                        <div id="new_user_household_id_error" class="text-danger small"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                        <div class="form-group">
                            <label class='form-label'>New Holder Status <span class="text-danger">*</span></label>
                            <select class="selectpicker form-control" name="new_holder_status_id" 
                                data-live-search="true" id="newHolderStatusSelected">
                                <option disabled selected>Choose one...</option>
                                @foreach(\App\Models\HouseholdStatus::where('is_archived', 0)->orderBy('status')->get() as $status)
                                    <option value="{{ $status->id }}">{{ $status->status }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Household status of the new holder</div>
                        </div>
                        <div id="new_holder_status_id_error" class="text-danger small"></div>
                    </div>
                </div>
            </div>
        </div>                        <!-- Dynamic Shared Fields -->
                        <div id="sharedFieldsDiv" class="card border-success mt-4" style="display: none;"> 
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bx bx-group me-2 text-success"></i>Shared Meter Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class='form-label'>Shared User Name</label>
                                            <select class="selectpicker form-control" name="shared_user_household_id" 
                                                data-live-search="true" id="sharedUserHouseholdSelected">
                                                <option disabled selected>Select current holder community first...</option>
                                            </select>
                                            <div class="form-text">Uses the same community as current holder</div>
                                        </div>
                                        <div id="shared_user_household_id_error" class="text-danger small"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Relocated Fields -->
                        <div id="relocatedFieldsDiv" class="card border-warning mt-4" style="display: none;"> 
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bx bx-map me-2 text-warning"></i>Relocation Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class='form-label'>New Community <span class="text-danger">*</span></label>
                                            <select class="selectpicker form-control" name="relocated_community_id" 
                                                data-live-search="true" id="relocatedCommunitySelected">
                                                <option disabled selected>Choose one...</option>
                                                @foreach($communities as $community)
                                                    <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Community where the meter has been relocated to</div>
                                        </div>
                                        <div id="relocated_community_id_error" class="text-danger small"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class='form-label'>
                                        <i class="bx bx-note me-1"></i>Notes 
                                        <small class="text-muted">(optional)</small>
                                    </label>
                                    <textarea name="notes" class="form-control" 
                                        rows="3" maxlength="1000" 
                                        placeholder="Add any additional notes or comments about this meter history change..."
                                        style="resize: vertical;">{{ old('notes') }}</textarea>
                                    <div class="form-text">
                                        <span id="notesCounter">0</span>/1000 characters
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Fields marked with <span class="text-danger">*</span> are required
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                            <i class="bx bx-refresh me-1"></i>Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="bx bx-save me-1"></i>Save New History
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript -->
<script>
// Reset Form Function
function resetForm() {
    document.getElementById('newMeterHistoryForm').reset();
    hideAllDynamicSections();
    clearAllErrors();
    updateNotesCounter();
    
    // Reset meter number fields
    const meterNumberInput = document.querySelector('input[name="current_meter_number"]');
    if (meterNumberInput) {
        meterNumberInput.value = '';
        meterNumberInput.placeholder = 'Select household first...';
        meterNumberInput.classList.remove('is-valid', 'is-invalid', 'auto-filled');
    }

    
    // Reset holder type to household (default)
    const householdRadio = document.getElementById('holderTypeHousehold');
    if (householdRadio) {
        householdRadio.checked = true;
        // Show household selection, hide public structure
        const householdSelectionDiv = document.getElementById('householdSelectionDiv');
        const publicStructureSelectionDiv = document.getElementById('publicStructureSelectionDiv');
        if (householdSelectionDiv) householdSelectionDiv.style.display = 'block';
        if (publicStructureSelectionDiv) publicStructureSelectionDiv.style.display = 'none';
    }
    
    // Reset selectpicker dropdowns and remove auto-fill classes
    if (typeof $ !== 'undefined') {
        $('.bootstrap-select').removeClass('auto-filled');
        $('.selectpicker').selectpicker('refresh');
    }
}

// Clear all error messages
function clearAllErrors() {
    const errorElements = document.querySelectorAll('[id$="_error"]');
    errorElements.forEach(element => {
        element.textContent = '';
    });
}

// Hide all dynamic sections
function hideAllDynamicSections() {
    const sections = ['replacementFieldsDiv', 'usedByOtherFieldsDiv', 'sharedFieldsDiv', 'relocatedFieldsDiv'];
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'none';
        }
    });
}

// Show dynamic section with animation
function showSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
        section.classList.add('dynamic-section');
        // Smooth scroll to the section
        setTimeout(() => {
            section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
}

// Update notes character counter
function updateNotesCounter() {
    const notesTextarea = document.querySelector('textarea[name="notes"]');
    const counter = document.getElementById('notesCounter');
    
    if (notesTextarea && counter) {
        const updateCount = () => {
            const count = notesTextarea.value.length;
            counter.textContent = count;
            counter.style.color = count > 900 ? '#e74c3c' : count > 800 ? '#f39c12' : '#6c757d';
        };
        
        notesTextarea.addEventListener('input', updateCount);
        updateCount(); // Initial count
    }
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize
    hideAllDynamicSections();
    updateNotesCounter();
    
    // Holder type radio button handler
    const holderTypeRadios = document.querySelectorAll('input[name="holder_type"]');
    const householdSelectionDiv = document.getElementById('householdSelectionDiv');
    const publicStructureSelectionDiv = document.getElementById('publicStructureSelectionDiv');
    const householdSelect = document.querySelector('select[name="current_holder_household_id"]');
    const publicStructureSelect = document.querySelector('select[name="current_holder_public_structure_id"]');
    
    if (holderTypeRadios.length > 0 && householdSelectionDiv && publicStructureSelectionDiv) {
        holderTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const meterNumberInput = document.querySelector('input[name="current_meter_number"]');
                
                if (this.value === 'household') {
                    // Show household selection, hide public structure
                    householdSelectionDiv.style.display = 'block';
                    publicStructureSelectionDiv.style.display = 'none';
                    
                    // Make household required, remove public structure requirement
                    if (householdSelect) {
                        householdSelect.setAttribute('required', 'required');
                    }
                    if (publicStructureSelect) {
                        publicStructureSelect.removeAttribute('required');
                        publicStructureSelect.value = '';
                        $(publicStructureSelect).selectpicker('refresh');
                    }
                    
                    // Clear meter number
                    if (meterNumberInput) {
                        meterNumberInput.value = '';
                        meterNumberInput.placeholder = 'Select household first...';
                        meterNumberInput.classList.remove('is-valid', 'is-invalid', 'auto-filled');
                    }
                    
                } else if (this.value === 'public') {
                    // Show public structure selection, hide household
                    householdSelectionDiv.style.display = 'none';
                    publicStructureSelectionDiv.style.display = 'block';
                    
                    // Make public structure required, remove household requirement
                    if (publicStructureSelect) {
                        publicStructureSelect.setAttribute('required', 'required');
                    }
                    if (householdSelect) {
                        householdSelect.removeAttribute('required');
                        householdSelect.value = '';
                        $(householdSelect).selectpicker('refresh');
                    }
                    
                    // Clear meter number
                    if (meterNumberInput) {
                        meterNumberInput.value = '';
                        meterNumberInput.placeholder = 'Select public structure first...';
                        meterNumberInput.classList.remove('is-valid', 'is-invalid', 'auto-filled');
                    }
                    
                }
            });
        });
        
    }
    
    // Status change handler
    const statusSelect = document.getElementById('meterStatusSelected');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex]?.text || '';
            
            // Hide all sections first
            hideAllDynamicSections();
            clearAllErrors();
            
            // Reset required attributes
            const newMeterInput = document.getElementById('newMeterNumberInput');
            const newUserHouseholdSelect = document.getElementById('newUserHouseholdSelected');
            const newHolderCommunitySelect = document.getElementById('newHolderCommunitySelected');
            const newHolderStatusSelect = document.getElementById('newHolderStatusSelected');
            const relocatedCommunitySelect = document.getElementById('relocatedCommunitySelected');
            
            // Remove required attributes from all dynamic fields
            if (newMeterInput) newMeterInput.removeAttribute('required');
            if (newUserHouseholdSelect) newUserHouseholdSelect.removeAttribute('required');
            if (newHolderCommunitySelect) newHolderCommunitySelect.removeAttribute('required');
            if (newHolderStatusSelect) newHolderStatusSelect.removeAttribute('required');
            if (relocatedCommunitySelect) relocatedCommunitySelect.removeAttribute('required');
            
            // Show appropriate section based on status
            if (selectedText === 'Replaced') {
                showSection('replacementFieldsDiv');
                // Make new meter number required for replacement
                if (newMeterInput) {
                    newMeterInput.setAttribute('required', 'required');
                }
            } else if (selectedText === 'Used By Other') {
                showSection('usedByOtherFieldsDiv');
                // Make used by other fields required
                if (newUserHouseholdSelect) newUserHouseholdSelect.setAttribute('required', 'required');
                if (newHolderCommunitySelect) newHolderCommunitySelect.setAttribute('required', 'required');
                if (newHolderStatusSelect) newHolderStatusSelect.setAttribute('required', 'required');
            } else if (selectedText === 'Become A Shared') {
                showSection('sharedFieldsDiv');
            } else if (selectedText === 'Relocated') {
                showSection('relocatedFieldsDiv');
                // Make relocated community field required
                if (relocatedCommunitySelect) relocatedCommunitySelect.setAttribute('required', 'required');
            }
        });
        
        // Trigger initial check if something is already selected
        if (statusSelect.value && statusSelect.selectedIndex > 0) {
            statusSelect.dispatchEvent(new Event('change'));
        }
        
    }
    
    // Community change handler for current holder household selection
    const communitySelect = document.querySelector('select[name="current_holder_community_id"]');
    const sharedUserHouseholdSelect = document.querySelector('select[name="shared_user_household_id"]');
    
    if (communitySelect && householdSelect) {
        communitySelect.addEventListener('change', function() {
            const communityId = this.value;
            
            // Reset household and public structure dropdowns
            householdSelect.innerHTML = '<option disabled selected>Loading households...</option>';
            $(householdSelect).selectpicker('refresh');
            
            if (publicStructureSelect) {
                publicStructureSelect.innerHTML = '<option disabled selected>Loading public structures...</option>';
                $(publicStructureSelect).selectpicker('refresh');
            }
            
            // Clear meter number field
            const meterNumberInput = document.querySelector('input[name="current_meter_number"]');
            if (meterNumberInput) {
                meterNumberInput.value = '';
                const selectedHolderType = document.querySelector('input[name="holder_type"]:checked');
                if (selectedHolderType && selectedHolderType.value === 'public') {
                    meterNumberInput.placeholder = 'Select public structure first...';
                } else {
                    meterNumberInput.placeholder = 'Select household first...';
                }
                meterNumberInput.classList.remove('is-valid', 'is-invalid', 'auto-filled');
            }
            
            // Also reset shared user household dropdown if it exists
            if (sharedUserHouseholdSelect) {
                sharedUserHouseholdSelect.innerHTML = '<option disabled selected>Loading households...</option>';
                $(sharedUserHouseholdSelect).selectpicker('refresh');
            }
            
            if (communityId) {
                // Fetch households first (this should always work)
                fetch(`/api/community/${communityId}/households`)
                    .then(response => response.json())
                    .then(householdsData => {
                        // Update current holder households
                        householdSelect.innerHTML = '<option disabled selected>Choose household...</option>';
                        
                        householdsData.forEach(household => {
                            const option = document.createElement('option');
                            option.value = household.id;
                            option.textContent = household.english_name;
                            // Store meter number as data attribute
                            if (household.meter_number) {
                                option.setAttribute('data-meter-number', household.meter_number);
                            }
                            householdSelect.appendChild(option);
                        });
                        
                        $(householdSelect).selectpicker('refresh');
                        
                        // Also update shared user households with same data
                        if (sharedUserHouseholdSelect) {
                            sharedUserHouseholdSelect.innerHTML = '<option disabled selected>Choose household...</option>';
                            
                            householdsData.forEach(household => {
                                const option = document.createElement('option');
                                option.value = household.id;
                                option.textContent = household.english_name;
                                // Store meter number as data attribute
                                if (household.meter_number) {
                                    option.setAttribute('data-meter-number', household.meter_number);
                                }
                                sharedUserHouseholdSelect.appendChild(option);
                            });
                            
                            $(sharedUserHouseholdSelect).selectpicker('refresh');
                        }
                        
                        // Now try to fetch public structures separately
                        if (publicStructureSelect) {
                            fetch(`/api/community/${communityId}/public-structures`)
                                .then(response => response.json())
                                .then(publicStructuresData => {
                                    publicStructureSelect.innerHTML = '<option disabled selected>Choose public structure...</option>';
                                    
                                    publicStructuresData.forEach(publicStructure => {
                                        const option = document.createElement('option');
                                        option.value = publicStructure.id;
                                        option.textContent = publicStructure.english_name;
                                        // Store meter number as data attribute
                                        if (publicStructure.meter_number) {
                                            option.setAttribute('data-meter-number', publicStructure.meter_number);
                                        }
                                        publicStructureSelect.appendChild(option);
                                    });
                                    
                                    $(publicStructureSelect).selectpicker('refresh');
                                })
                                .catch(error => {
                                    publicStructureSelect.innerHTML = '<option disabled selected>No public structures available</option>';
                                    $(publicStructureSelect).selectpicker('refresh');
                                });
                        }
                    })
                    .catch(error => {
                        householdSelect.innerHTML = '<option disabled selected>Error loading households</option>';
                        $(householdSelect).selectpicker('refresh');
                        
                        if (sharedUserHouseholdSelect) {
                            sharedUserHouseholdSelect.innerHTML = '<option disabled selected>Error loading households</option>';
                            $(sharedUserHouseholdSelect).selectpicker('refresh');
                        }
                    });
            } else {
                householdSelect.innerHTML = '<option disabled selected>Choose community first...</option>';
                $(householdSelect).selectpicker('refresh');
                
                if (publicStructureSelect) {
                    publicStructureSelect.innerHTML = '<option disabled selected>Choose community first...</option>';
                    $(publicStructureSelect).selectpicker('refresh');
                }
                
                // Clear meter number field
                const meterNumberInput = document.querySelector('input[name="current_meter_number"]');
                if (meterNumberInput) {
                    meterNumberInput.value = '';
                    const selectedHolderType = document.querySelector('input[name="holder_type"]:checked');
                    if (selectedHolderType && selectedHolderType.value === 'public') {
                        meterNumberInput.placeholder = 'Select public structure first...';
                    } else {
                        meterNumberInput.placeholder = 'Select household first...';
                    }
                    meterNumberInput.classList.remove('is-valid', 'is-invalid', 'auto-filled');
                }
                
                if (sharedUserHouseholdSelect) {
                    sharedUserHouseholdSelect.innerHTML = '<option disabled selected>Select current holder community first...</option>';
                    $(sharedUserHouseholdSelect).selectpicker('refresh');
                }
            }
        });
    }
    
    // Current holder household change handler for meter number auto-fill
    if (householdSelect) {
        householdSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const meterNumberInput = document.querySelector('input[name="current_meter_number"]');
            
            if (selectedOption && meterNumberInput) {
                const meterNumber = selectedOption.getAttribute('data-meter-number');
                
                if (meterNumber) {
                    meterNumberInput.value = meterNumber;
                    // Clear any validation errors and add auto-filled styling
                    meterNumberInput.classList.remove('is-invalid');
                    meterNumberInput.classList.add('is-valid', 'auto-filled');
                    document.getElementById('current_meter_number_error').textContent = '';
                    
                    // Remove the auto-filled class after animation
                    setTimeout(() => {
                        meterNumberInput.classList.remove('auto-filled');
                    }, 2000);
                    
                } else {
                    meterNumberInput.value = '';
                    meterNumberInput.placeholder = "No meter number available";
                }
            }
        });
    }
    
    // Current holder public structure change handler for meter number auto-fill
    if (publicStructureSelect) {
        publicStructureSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const meterNumberInput = document.querySelector('input[name="current_meter_number"]');
            
            if (selectedOption && meterNumberInput) {
                const meterNumber = selectedOption.getAttribute('data-meter-number');
                
                if (meterNumber) {
                    meterNumberInput.value = meterNumber;
                    // Clear any validation errors and add auto-filled styling
                    meterNumberInput.classList.remove('is-invalid');
                    meterNumberInput.classList.add('is-valid', 'auto-filled');
                    document.getElementById('current_meter_number_error').textContent = '';
                    
                    // Remove the auto-filled class after animation
                    setTimeout(() => {
                        meterNumberInput.classList.remove('auto-filled');
                    }, 2000);
                    
                } else {
                    meterNumberInput.value = '';
                    meterNumberInput.placeholder = "No meter number available";
                }
            }
        });
    }
    
    // New Holder Community change handler for "Used By Other" section
    const newHolderCommunitySelect = document.querySelector('select[name="new_holder_community_id"]');
    const newUserHouseholdSelect = document.getElementById('newUserHouseholdSelected');
    
    if (newHolderCommunitySelect && newUserHouseholdSelect) {
        newHolderCommunitySelect.addEventListener('change', function() {
            const communityId = this.value;
            
            // Reset new user household dropdown
            newUserHouseholdSelect.innerHTML = '<option disabled selected>Loading households...</option>';
            $(newUserHouseholdSelect).selectpicker('refresh');
            
            if (communityId) {
                // Fetch households for selected community
                fetch(`/api/community/${communityId}/households`)
                    .then(response => response.json())
                    .then(data => {
                        newUserHouseholdSelect.innerHTML = '<option disabled selected>Choose household...</option>';
                        
                        data.forEach(household => {
                            const option = document.createElement('option');
                            option.value = household.id;
                            option.textContent = household.english_name;
                            // Store meter number as data attribute
                            if (household.meter_number) {
                                option.setAttribute('data-meter-number', household.meter_number);
                            }
                            newUserHouseholdSelect.appendChild(option);
                        });
                        
                        $(newUserHouseholdSelect).selectpicker('refresh');
                    })
                    .catch(error => {
                        newUserHouseholdSelect.innerHTML = '<option disabled selected>Error loading households</option>';
                        $(newUserHouseholdSelect).selectpicker('refresh');
                    });
            } else {
                newUserHouseholdSelect.innerHTML = '<option disabled selected>Choose community first...</option>';
                $(newUserHouseholdSelect).selectpicker('refresh');
            }
        });
    }
    

    
    // Reason dropdown change handler for auto-setting household status
    const reasonSelect = document.getElementById('meterReasonSelected');
    const householdStatusSelect = document.querySelector('select[name="current_holder_household_status_id"]');
    
    if (reasonSelect && householdStatusSelect) {
        reasonSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption && selectedOption.textContent) {
                const reasonText = selectedOption.textContent.trim();
                
                // Check if "User Left" is selected
                if (reasonText === 'User Left') {
                    // Find the "Left" status in household status dropdown
                    const householdStatusOptions = householdStatusSelect.options;
                    
                    for (let i = 0; i < householdStatusOptions.length; i++) {
                        const statusText = householdStatusOptions[i].textContent.trim();
                        
                        if (statusText === 'Left') {
                            // Set the household status to "Left"
                            householdStatusSelect.value = householdStatusOptions[i].value;
                            $(householdStatusSelect).selectpicker('refresh');
                            
                            break;
                        }
                    }
                }
            }
        });
    }
    
    // Form validation with enhanced UX
    const form = document.getElementById('newMeterHistoryForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            clearAllErrors();
            
            // Show loading state
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...';
            submitBtn.disabled = true;
            
            // Validate status selection
            if (!statusSelect.value) {
                document.getElementById('meter_history_status_id_error').textContent = 'Please select a status.';
                statusSelect.focus();
                isValid = false;
            }
            
            // Validate date
            const dateInput = document.querySelector('input[name="date"]');
            if (!dateInput.value) {
                dateInput.classList.add('is-invalid');
                isValid = false;
            }
            
            // Validate Current Holder Information
            const currentMeterNumber = document.querySelector('input[name="current_meter_number"]');
            if (!currentMeterNumber.value.trim()) {
                document.getElementById('current_meter_number_error').textContent = 'Current meter number is required.';
                if (isValid) currentMeterNumber.focus();
                isValid = false;
            }
            
            const currentCommunity = document.querySelector('select[name="current_holder_community_id"]');
            if (!currentCommunity.value) {
                document.getElementById('current_holder_community_id_error').textContent = 'Please select a community.';
                if (isValid) currentCommunity.focus();
                isValid = false;
            }
            
            // Validate holder selection based on holder type
            const selectedHolderType = document.querySelector('input[name="holder_type"]:checked');
            if (selectedHolderType) {
                if (selectedHolderType.value === 'household') {
                    const currentHousehold = document.querySelector('select[name="current_holder_household_id"]');
                    if (!currentHousehold.value) {
                        document.getElementById('current_holder_household_id_error').textContent = 'Please select a household name.';
                        if (isValid) currentHousehold.focus();
                        isValid = false;
                    }
                } else if (selectedHolderType.value === 'public') {
                    const currentPublicStructure = document.querySelector('select[name="current_holder_public_structure_id"]');
                    if (!currentPublicStructure.value) {
                        document.getElementById('current_holder_public_structure_id_error').textContent = 'Please select a public structure.';
                        if (isValid) currentPublicStructure.focus();
                        isValid = false;
                    }
                }
            } else {
                document.getElementById('holder_type_error').textContent = 'Please select a holder type.';
                isValid = false;
            }
            
            const currentHouseholdStatus = document.querySelector('select[name="current_holder_household_status_id"]');
            if (!currentHouseholdStatus.value) {
                document.getElementById('current_holder_household_status_id_error').textContent = 'Please select a household status.';
                if (isValid) currentHouseholdStatus.focus();
                isValid = false;
            }
            
            // Status-specific validation
            const selectedText = statusSelect.options[statusSelect.selectedIndex]?.text;
            if (selectedText === 'Replaced') {
                const newMeterInput = document.getElementById('newMeterNumberInput');
                if (!newMeterInput.value.trim()) {
                    document.getElementById('new_meter_number_error').textContent = 'New meter number is required for replacement status.';
                    newMeterInput.focus();
                    isValid = false;
                }
            } else if (selectedText === 'Used By Other') {
                // Validate new community
                const newHolderCommunity = document.getElementById('newHolderCommunitySelected');
                if (!newHolderCommunity.value) {
                    document.getElementById('new_holder_community_id_error').textContent = 'New community is required when meter is used by other.';
                    if (isValid) newHolderCommunity.focus();
                    isValid = false;
                }
                
                // Validate new user household
                const newUserHousehold = document.getElementById('newUserHouseholdSelected');
                if (!newUserHousehold.value) {
                    document.getElementById('new_user_household_id_error').textContent = 'New user household is required when meter is used by other.';
                    if (isValid) newUserHousehold.focus();
                    isValid = false;
                }
                

                
                // Validate new holder status
                const newHolderStatus = document.getElementById('newHolderStatusSelected');
                if (!newHolderStatus.value) {
                    document.getElementById('new_holder_status_id_error').textContent = 'New holder status is required when meter is used by other.';
                    if (isValid) newHolderStatus.focus();
                    isValid = false;
                }
            } else if (selectedText === 'Relocated') {
                // Validate relocated community
                const relocatedCommunity = document.getElementById('relocatedCommunitySelected');
                if (!relocatedCommunity.value) {
                    document.getElementById('relocated_community_id_error').textContent = 'New community is required when meter is relocated.';
                    if (isValid) relocatedCommunity.focus();
                    isValid = false;
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                // Restore button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                // Scroll to first error
                const firstError = document.querySelector('.text-danger:not(:empty)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                return false;
            }
            
        });
    }
});
</script>

@endsection

@section('page-script')
<script>
// Initialize selectpicker if jQuery is available
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        // Initialize bootstrap-select
        $('.selectpicker').selectpicker({
            style: 'btn-outline-secondary',
            size: 4,
            liveSearch: true,
            showSubtext: true
        });
        
        // Add change handlers for selectpicker validation
        $('.selectpicker').on('change', function() {
            const select = this;
            const errorDiv = document.getElementById(select.name + '_error');
            
            if (select.hasAttribute('required') && !select.value) {
                $(select).addClass('is-invalid');
                if (errorDiv) {
                    errorDiv.textContent = 'Please select an option.';
                }
            } else {
                $(select).removeClass('is-invalid').addClass('is-valid');
                if (errorDiv) {
                    errorDiv.textContent = '';
                }
            }
        });
        
    });
}

// Additional form enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add real-time validation feedback
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
                // Show error message for the specific field
                const errorDiv = document.getElementById(this.name + '_error');
                if (errorDiv) {
                    if (this.tagName === 'SELECT') {
                        errorDiv.textContent = 'Please select an option.';
                    } else {
                        errorDiv.textContent = 'This field is required.';
                    }
                }
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                // Clear error message
                const errorDiv = document.getElementById(this.name + '_error');
                if (errorDiv) {
                    errorDiv.textContent = '';
                }
            }
        });
        
        input.addEventListener('change', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                // Clear error message
                const errorDiv = document.getElementById(this.name + '_error');
                if (errorDiv) {
                    errorDiv.textContent = '';
                }
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                // Clear error message
                const errorDiv = document.getElementById(this.name + '_error');
                if (errorDiv) {
                    errorDiv.textContent = '';
                }
            }
        });
    });
    
    // Auto-save to localStorage for form recovery
    const form = document.getElementById('newMeterHistoryForm');
    if (form) {
        // Load saved data
        const savedData = localStorage.getItem('meterHistoryFormData');
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const element = form.querySelector(`[name="${key}"]`);
                    if (element && element.type !== 'date') {
                        element.value = data[key];
                    }
                });
            } catch (e) {
                // Could not restore form data
            }
        }
        
        // Save data on input
        form.addEventListener('input', function() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            localStorage.setItem('meterHistoryFormData', JSON.stringify(data));
        });
        
        // Clear saved data on successful submit
        form.addEventListener('submit', function() {
            localStorage.removeItem('meterHistoryFormData');
        });
    }
    
});
</script>
@endsection
